// ==============================================================================
// 3. Afia Lubna Purnota (Branch: feature/database-config)
// Target File: server/config/db.js
// Exact Requirements:
//  - Configure MySQL connection pool using mysql2/promise.
//  - Implement environment variable handling (dotenv) so database credentials are not hardcoded.
// ==============================================================================

const mysql = require('mysql2/promise');
require('dotenv').config();

const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    port: process.env.DB_PORT || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'unimarket_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

module.exports = pool;
