import React, {useState} from 'react';
import axios from 'axios';
import {Container, Row, Col} from 'react-bootstrap';
import {Link, useNavigate} from 'react-router-dom';
import styles from  '../assets/landing.module.css'; // Import the hook

const RecoverPassword = () => {
    const [email, setEmail] = useState('');
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const navigate = useNavigate();

    const handleRecover = async (e) => {
        e.preventDefault();
        setError('');
        setMessage('');
        setIsLoading(true);

        try {
            const response = await axios.post('https://beewords.ir/api/recover-password', {email});

            const {status, message: resMessage, errors} = response.data;

            if (status === 200) {
                setMessage('A password recovery link has been sent to your email.');
                setEmail(''); // Clear the email field after success
            } else if (status === 400 && errors) {
                const errorMessages = Object.values(errors).join(' ');
                setError(errorMessages);
            } else {
                setError('An unexpected error occurred. Please try again.');
            }
        } catch (err) {
            if (err.response) {
                const {status, data} = err.response;
                if (status === 429) {
                    setError('You have sent too many password recovery requests. Please try again later.');
                } else if (status === 400 && data.errors) {
                    const errorMessages = Object.values(data.errors).join(' ');
                    setError(errorMessages);
                } else if (data && data.message) {
                    setError(data.message);
                } else {
                    setError('An unexpected error occurred. Please try again.');
                }
            } else {
                setError('Could not connect to the server. Please try again.');
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Container fluid className=" auth-container d-flex justify-content-center align-items-center min-vh-100">
            <Row className="w-100">
                <Col xs={12} sm={8} md={6} lg={4} className="mx-auto">
                    <div className="auth-box p-4 shadow rounded ">
                        <h3 className="text-center mb-4">Recover Password</h3>
                        <form onSubmit={handleRecover}>
                            <div className="mb-3">
                                <input
                                    type="email"
                                    className="form-control authInput"
                                    placeholder="Your email address"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    required
                                />
                            </div>
                            {error && <p className="text-danger text-center">{error}</p>}
                            {message && <p className="text-success text-center">{message}</p>}
                            <button type="submit" disabled={isLoading} className={styles.pushable}>
                                <span className={styles.shadow}></span>
                                <span className={styles.edge}></span>
                                <span
                                    className={`${styles.front} authPushBtn`}> {isLoading ? 'Sending...' : 'Send Recovery Link'}</span>
                            </button>
                        </form>
                        <div className="auth-links text-center mt-3">
                            <Link to="/auth" className="recover-password-link">
                                Back to Login
                            </Link>
                        </div>
                    </div>
                </Col>
            </Row>
        </Container>
    );
};

export default RecoverPassword;