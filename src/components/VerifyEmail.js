import React, { useState } from 'react';
import axios from 'axios';

const VerifyEmail = () => {
    const [verificationCode, setVerificationCode] = useState('');
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleVerify = async (e) => {
        e.preventDefault();
        setError('');
        setMessage('');
        setIsLoading(true);

        try {
            const response = await axios.post('https://beewords.ir/api/verify', { code: verificationCode });
            const { status, message } = response.data;

            if (status === 200) {
                setMessage('Email verified successfully! You can now log in.');
            } else {
                setError(message);
            }
        } catch (err) {
            setError('Failed to connect to the server. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="verify-container">
            <h2>Verify Email</h2>
            <form onSubmit={handleVerify}>
                <div className="form-group">
                    <label>Verification Code</label>
                    <input
                        type="text"
                        className="form-control"
                        value={verificationCode}
                        onChange={(e) => setVerificationCode(e.target.value)}
                        required
                    />
                </div>
                {error && <p className="text-danger">{error}</p>}
                {message && <p className="text-success">{message}</p>}
                <button type="submit" className="btn btn-primary" disabled={isLoading}>
                    {isLoading ? 'Verifying...' : 'Verify'}
                </button>
            </form>
        </div>
    );
};

export default VerifyEmail;
