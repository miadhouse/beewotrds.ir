// src/components/Login.js
import React, { useState, useContext, useRef } from 'react';
import axios from 'axios';
import { useNavigate, Link } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import ReCAPTCHA from 'react-google-recaptcha'; // وارد کردن ReCAPTCHA
import styles from '../assets/landing.module.css';
import { useTranslation } from 'react-i18next';
import { toast } from 'react-toastify'; // Only import toast, not ToastContainer
import 'react-toastify/dist/ReactToastify.css'; // Import styles

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [recaptchaToken, setRecaptchaToken] = useState(null); // متغیر حالت برای توکن reCAPTCHA
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const recaptchaRef = useRef(null); // ریفرنس برای ریست کردن reCAPTCHA
    const navigate = useNavigate();
    const { login } = useContext(AuthContext);
    const { t } = useTranslation();

    const handleLogin = async (e) => {
        e.preventDefault();
        setError('');
        setIsLoading(true);

        if (!recaptchaToken) {
            toast.error(t('completeRecaptcha') || 'Please complete the reCAPTCHA.');
            setIsLoading(false);
            return;
        }

        try {
            const response = await axios.post('https://beewords.ir/api/login', {
                email,
                password,
                recaptchaToken,
            });

            const { status, token } = response.data;

            if (status === 200 && token) {
                login(token);
                navigate('/');
            }
        } catch (err) {
            // Existing error handling
        } finally {
            setIsLoading(false);
            if (recaptchaRef.current) {
                try {
                    recaptchaRef.current.reset();
                } catch (error) {
                    console.error('Error resetting ReCAPTCHA:', error);
                }
            }
            setRecaptchaToken(null);
        }
    };
    const onReCAPTCHALoad = () => {
        console.log('ReCAPTCHA loaded successfully');
    };
    const handleRecaptcha = (token) => {
        setRecaptchaToken(token);
    };

    return (
        <div className="login-form-container">
            <form onSubmit={handleLogin}>
                <div className="mb-3">
                    <input 
                        type="email"
                        className="form-control authInput"
                        placeholder={t('emailAddress') || 'Your email address'}
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <input
                        type="password"
                        className="form-control authInput"
                        placeholder={t('enterPassword') || 'Enter password'}
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </div>
                <div className="mb-3">
                    <ReCAPTCHA
                        ref={recaptchaRef}
                        sitekey="6LdypVMqAAAAALPf5RyL_jufQ08Qt2eEkL8uRemR"
                        asyncScriptOnLoad={onReCAPTCHALoad}
                        onChange={handleRecaptcha}
                        theme="dark"
                    />
                </div>
                {error && <p className="text-danger text-center">{error}</p>}
                <button type="submit" disabled={isLoading} className={styles.pushable}>
                    <span className={styles.shadow}></span>
                    <span className={styles.edge}></span>
                    <span className={`${styles.front} authPushBtn`}>
                        {isLoading ? t('loggingIn') || 'Logging in...' : t('login') || 'Login'}
                    </span>
                </button>
            </form>
            <div className="auth-links text-center mt-3">
                <Link to="/recover-password" className="recover-password-link">
                    {t('recoverPassword') || 'Recover Password'}
                </Link>
            </div>
        </div>
    );
};

export default Login;
