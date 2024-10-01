import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useLocation, useNavigate } from 'react-router-dom';

const VerifyEmail = () => {
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [email, setEmail] = useState('');  // Storing the user-entered email
    const [showResend, setShowResend] = useState(false);  // State to show the resend email button
    const location = useLocation();
    const navigate = useNavigate();

    // Extracting the verification code from the URL
    const queryParams = new URLSearchParams(location.search);
    const verificationCode = queryParams.get('code');  // Getting the "code" value from the URL

    useEffect(() => {
        const handleVerify = async () => {
            setError('');
            setMessage('');
            setIsLoading(true);

            try {
                // Sending a GET request to the server for email verification
                const response = await axios.get(`https://beewords.ir/api/verify?code=${verificationCode}`);
                const { status, message } = response.data;

                if (status === 200) {
                    setMessage('Email verified successfully! Redirecting to login...');
                    // Redirect to the login page after 2 seconds
                    setTimeout(() => {
                        navigate('/login');
                    }, 2000);
                } else {
                    setError(message);
                    setShowResend(true);  // If the code is invalid, show the resend button
                }
            } catch (err) {
                setError('Failed to connect to the server. Please try again.');
            } finally {
                setIsLoading(false);
            }
        };

        if (verificationCode) {
            handleVerify();  // If the verification code is present in the URL, execute the verification process
        } else {
            setError('No verification code provided.');
            setIsLoading(false);
        }
    }, [verificationCode, navigate]);

    // Function to resend the verification email
    const handleResend = async () => {
        if (!email) {
            setError('Please enter your email address.');
            return;
        }

        setIsLoading(true);
        setError('');
        setMessage('');
        try {
            // Sending a POST request to the server to resend the verification email
            const response = await axios.post('https://beewords.ir/api/resend-verification', {
                email: email  // The email entered by the user
            });
            const { status, message } = response.data;
            if (status === 200) {
                setMessage('Verification email resent successfully. Please check your inbox.');
            } else {
                setError(message);
            }
        } catch (err) {
            setError('Failed to resend verification email. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="verify-container">
            <h2>Verify Email</h2>
            {isLoading ? (
                <p>Verifying your email...</p>
            ) : error ? (
                <>
                    <p className="text-danger">{error}</p>
                    {showResend && (
                        <>
                            <div className="form-group">
                                <label htmlFor="email">Enter your email to resend verification:</label>
                                <input
                                    type="email"
                                    id="email"
                                    className="form-control"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}  // Storing the entered value
                                    required
                                />
                            </div>
                            <button className="btn btn-secondary" onClick={handleResend} disabled={isLoading}>
                                {isLoading ? 'Resending...' : 'Resend Verification Email'}
                            </button>
                        </>
                    )}
                </>
            ) : (
                <p className="text-success">{message}</p>
            )}
        </div>
    );
};

export default VerifyEmail;
