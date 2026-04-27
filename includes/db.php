<?php
/**
 * Database Configuration and Functions
 * Handles all database connections and queries
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'laberion_wms');
define('DB_PORT', 3306);

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (!$conn) {
    error_log("Database Connection Failed: " . mysqli_connect_error());
    die("Database connection failed. Please try again later.");
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Set timezone
date_default_timezone_set('Europe/Zurich');

/**
 * Sanitize user input
 * @param string $data - Raw input data
 * @return string - Sanitized data
 */
function clean_input($data) {
    global $conn;
    
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Execute database query safely
 * @param string $query - SQL query
 * @return mixed - Query result or false
 */
function db_query($query) {
    global $conn;
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Database Query Error: " . mysqli_error($conn));
        error_log("Query: " . $query);
        return false;
    }
    
    return $result;
}

/**
 * Get single row from query
 * @param string $query - SQL query
 * @return array - Single row as associative array
 */
function db_fetch_one($query) {
    $result = db_query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Get all rows from query
 * @param string $query - SQL query
 * @return array - All rows as array of associative arrays
 */
function db_fetch_all($query) {
    $result = db_query($query);
    $rows = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    
    return $rows;
}

/**
 * Insert data into database
 * @param string $table - Table name
 * @param array $data - Data to insert (key => value)
 * @return int - Insert ID or 0 on failure
 */
function db_insert($table, $data) {
    global $conn;
    
    $columns = array_keys($data);
    $values = array_values($data);
    
    // Sanitize values
    $values = array_map(function($val) {
        global $conn;
        if ($val === null) {
            return 'NULL';
        }
        return "'" . mysqli_real_escape_string($conn, $val) . "'";
    }, $values);
    
    $columns_str = implode(', ', $columns);
    $values_str = implode(', ', $values);
    
    $query = "INSERT INTO $table ($columns_str) VALUES ($values_str)";
    
    if (db_query($query)) {
        return mysqli_insert_id($conn);
    }
    
    return 0;
}

/**
 * Update data in database
 * @param string $table - Table name
 * @param array $data - Data to update (key => value)
 * @param string $where - WHERE clause
 * @return bool - Success or failure
 */
function db_update($table, $data, $where) {
    global $conn;
    
    $set_parts = [];
    
    foreach ($data as $key => $val) {
        if ($val === null) {
            $set_parts[] = "$key = NULL";
        } else {
            $escaped_val = mysqli_real_escape_string($conn, $val);
            $set_parts[] = "$key = '$escaped_val'";
        }
    }
    
    $set_str = implode(', ', $set_parts);
    $query = "UPDATE $table SET $set_str WHERE $where";
    
    return db_query($query) ? true : false;
}

/**
 * Delete data from database
 * @param string $table - Table name
 * @param string $where - WHERE clause
 * @return bool - Success or failure
 */
function db_delete($table, $where) {
    $query = "DELETE FROM $table WHERE $where";
    return db_query($query) ? true : false;
}

/**
 * Count rows in table
 * @param string $table - Table name
 * @param string $where - WHERE clause (optional)
 * @return int - Number of rows
 */
function db_count($table, $where = '1=1') {
    $result = db_fetch_one("SELECT COUNT(*) as count FROM $table WHERE $where");
    return $result ? $result['count'] : 0;
}

/**
 * Get last error
 * @return string - Last database error
 */
function db_error() {
    global $conn;
    return mysqli_error($conn);
}

/**
 * Close database connection
 */
function db_close() {
    global $conn;
    if ($conn) {
        mysqli_close($conn);
    }
}

// Register shutdown function to close connection
register_shutdown_function('db_close');
?>