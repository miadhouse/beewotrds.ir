import React, { useState, useContext, useRef, useEffect } from 'react';
import { Tabs, Tab, Container, Row, Col } from 'react-bootstrap';
import { Navigate } from 'react-router-dom';
import Login from './Login';
import Register from './Register';
import '../assets/AuthPage.css';
import { AuthContext } from '../context/AuthContext';
import Lottie from 'lottie-react';
import splashAnimation from '../assets/splash.json';
import { useTranslation } from 'react-i18next';

const Auth = () => {
    const [key, setKey] = useState('login');
    const { isAuthenticated } = useContext(AuthContext);
    const lottieRef = useRef(null);
    const { t } = useTranslation();
    // Constants based on animation
    const fps = 30;
    const initialEndFrame = 5.25 * fps;
    const loopStartFrame = initialEndFrame;
    const loopEndFrame = 7 * fps;

    const handleComplete = () => {
        if (lottieRef.current) {
            lottieRef.current.setDirection(1); // Ensure forward direction
            lottieRef.current.playSegments([loopStartFrame, loopEndFrame], true);
        }
    };

    useEffect(() => {
        if (lottieRef.current) {
            // Start by playing the initial segment
            lottieRef.current.playSegments([0, initialEndFrame], true);
        }
    }, []);

    if (isAuthenticated) {
        return <Navigate to="/" />;
    }
    return (
        <div className="auth-container">
            <Lottie
                lottieRef={lottieRef}
                loop={false} // Disable default looping
                autoplay={false} // Control playback manually
                animationData={splashAnimation}
                className="lottie-background"
                onComplete={handleComplete} // Use onComplete callback
            />
            <Container
                fluid
                className="d-flex justify-content-center align-items-center min-vh-100"
            >
                <Row className="w-100 auth-holder">
                    <Col xs={12} sm={8} md={6} lg={4} className="mx-auto">
                        <div className="auth-box p-4 rounded">
                            <Tabs
                                id="auth-tabs"
                                activeKey={key}
                                onSelect={(k) => setKey(k)}
                                className="mb-3"
                                justify
                            >
                                <Tab className='authNavTanBtn' eventKey="login" title={t('login')}>
                                    <Login />
                                </Tab>
                                <Tab className='authNavTanBtn' eventKey="register" title={t('register')}>
                                    <Register />
                                </Tab>
                            </Tabs>
                        </div>
                    </Col>
                </Row>
            </Container>
        </div>
    );
};

export default Auth;
