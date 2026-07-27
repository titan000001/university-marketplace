// ==============================================================================
// 2. Safa (Branch: feature/security-auth)
// Target File: server/controllers/authController.js
// Exact Requirements:
//  - Build Registration endpoint logic using bcryptjs with exactly 10 salt rounds.
//  - Build Login endpoint logic generating and issuing a JSON Web Token (JWT).
// ==============================================================================

const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const pool = require('../config/db');

const register = async (req, res) => {
    try {
        const { full_name, email, password } = req.body;

        if (!email || !password || !full_name) {
            return res.status(400).json({ error: 'All fields (full_name, email, password) are required.' });
        }

        if (password.length < 6) {
            return res.status(400).json({ error: 'Password must be at least 6 characters long.' });
        }

        // Check if user exists
        const [existingUsers] = await pool.query('SELECT id FROM User WHERE email = ?', [email]);
        if (existingUsers.length > 0) {
            return res.status(409).json({ error: 'User with this email already exists.' });
        }

        // Encrypt password using bcryptjs with exactly 10 salt rounds
        const saltRounds = 10;
        const password_hash = await bcrypt.hash(password, saltRounds);

        // Insert new user
        const [result] = await pool.query(
            'INSERT INTO User (full_name, email, password_hash) VALUES (?, ?, ?)',
            [full_name, email, password_hash]
        );

        return res.status(201).json({
            message: 'User registered successfully!',
            userId: result.insertId
        });
    } catch (error) {
        return res.status(500).json({ error: 'Server error during registration.', details: error.message });
    }
};

const login = async (req, res) => {
    try {
        const { email, password } = req.body;

        if (!email || !password) {
            return res.status(400).json({ error: 'Email and password are required.' });
        }

        // Fetch user by email
        const [users] = await pool.query('SELECT * FROM User WHERE email = ?', [email]);
        if (users.length === 0) {
            return res.status(401).json({ error: 'Invalid email or password.' });
        }

        const user = users[0];

        // Verify password
        const isMatch = await bcrypt.compare(password, user.password_hash);
        if (!isMatch) {
            return res.status(401).json({ error: 'Invalid email or password.' });
        }

        // Generate and issue JWT
        const tokenSecret = process.env.JWT_SECRET || 'unimarket_secret_key';
        const token = jwt.sign(
            { userId: user.id, email: user.email },
            tokenSecret,
            { expiresIn: '24h' }
        );

        return res.status(200).json({
            message: 'Login successful!',
            token,
            user: {
                id: user.id,
                full_name: user.full_name,
                email: user.email
            }
        });
    } catch (error) {
        return res.status(500).json({ error: 'Server error during login.', details: error.message });
    }
};

module.exports = {
    register,
    login
};
