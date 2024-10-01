// src/components/ResetPassword.js
import React, {useState} from 'react';
import axios from 'axios';
import {Container, Row, Col} from 'react-bootstrap';
import {Link, useLocation, useNavigate} from 'react-router-dom';
import styles from  '../assets/landing.module.css'; // Import the hook

const ResetPassword = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const queryParams = new URLSearchParams(location.search);
    const token = queryParams.get('token');

    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleReset = async (e) => {
        e.preventDefault();
        setError('');
        setMessage('');

        if (password !== confirmPassword) {
            setError('Password and confirmation do not match.');
            return;
        }

        setIsLoading(true);

        try {
            const response = await axios.post('https://beewords.ir/api/reset-password', {token, password});

            const {status, message: resMessage, errors} = response.data;

            if (status === 200) {
                setMessage('Password successfully reset. You can now log in.');
                setTimeout(() => navigate('/auth'), 3000);
            } else if (status === 400 && errors) {
                const errorMessages = Object.values(errors).join(' ');
                setError(errorMessages);
            } else {
                setError(resMessage || 'An unexpected error occurred. Please try again.');
            }
        } catch (err) {
            setError('Could not connect to the server. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    if (!token) {
        return (
            <Container fluid className=" auth-container d-flex justify-content-center align-items-center min-vh-100">
                <Row className="w-100">
                    <Col xs={12} sm={8} md={6} lg={4} className="mx-auto">
                        <div className="auth-box p-4 shadow rounded">
                            <h3 className="text-center mb-4">Error</h3>
                            <p>The password reset token is invalid.</p>
                            <div className="auth-links text-center mt-3">
                                <Link to="/recover-password" className="recover-password-link">
                                    Request password reset again
                                </Link>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        );
    }

    return (
        <Container fluid className=" auth-container d-flex justify-content-center align-items-center min-vh-100">
            <Row className="w-100">
                <Col xs={12} sm={8} md={6} lg={4} className="mx-auto">
                    <div className="auth-box p-4 shadow rounded bg-white">
                        <h3 className="text-center mb-4">Set New Password</h3>
                        <form onSubmit={handleReset}>
                            <div className="mb-3">
                                <input
                                    type="password"
                                    className="form-control authInput"
                                    placeholder="New Password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    required
                                />
                            </div>
                            <div className="mb-3">
                                <input
                                    type="password"
                                    className="form-control authInput"
                                    placeholder="Confirm New Password"
                                    value={confirmPassword}
                                    onChange={(e) => setConfirmPassword(e.target.value)}
                                    required
                                />
                            </div>
                            {error && <p className="text-danger text-center">{error}</p>}
                            {message && <p className="text-success text-center">{message}</p>}
                            <button type="submit" disabled={isLoading} className={styles.pushable}>
                                <span className={styles.shadow}></span>
                                <span className={styles.edge}></span>
                                <span
                                    className={`${styles.front} authPushBtn`}>{isLoading ? 'Setting...' : 'Set Password'}</span>
                            </button>
                        </form>
                        <div className="auth-links text-center mt-3">
                            <Link to="/auth" className="recover-password-link">
                                Request password reset again
                            </Link>
                        </div>
                    </div>
                </Col>
            </Row>
        </Container>
    );
};

export default ResetPassword;
