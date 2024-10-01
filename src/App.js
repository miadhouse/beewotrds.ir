// src/App.js
import React from 'react';
import { BrowserRouter as Router, Route, Routes, Navigate, Outlet } from 'react-router-dom';
import Header from './components/Header';
import Profile from './components/Profile';
import Navigation from './components/Navigation';
import Auth from './components/Auth';
import VerifyPage from './components/VerifyPage';
import RecoverPassword from './components/RecoverPassword';
import ResetPassword from './components/ResetPassword';
import { AuthProvider, AuthContext } from './context/AuthContext';
import { useContext } from 'react';
import MainContent from "./components/MainContent";
import { useTranslation } from 'react-i18next';
import './i18n'; // Import the i18n configuration

const ProtectedRoute = () => {
    const { isAuthenticated, loading } = useContext(AuthContext);
    if (loading) return <div>Loading...</div>;
    return isAuthenticated ? <Outlet /> : <Navigate to="/auth" />;
};

const Layout = () => {
    return (
        <>
            <Header />
            <Profile />
            <Outlet />
            <Navigation />
        </>
    );
};

const App = () => {
    const { i18n } = useTranslation();
    const dir = i18n.language === 'fa' ? 'rtl' : 'ltr';

    return (
        <AuthProvider>
            <Router>
                <div className="App" dir={dir}>
                    <Routes>
                        {/* مسیرهای عمومی */}
                        <Route path="/auth" element={<Auth />} />
                        <Route path="/verify" element={<VerifyPage />} />
                        <Route path="/recover-password" element={<RecoverPassword />} />
                        <Route path="/reset-password" element={<ResetPassword />} />
                        <Route path="/login" element={<Navigate to="/auth" />} />
                        <Route path="/register" element={<Navigate to="/auth" />} />

                        {/* مسیرهای محافظت شده */}
                        <Route element={<ProtectedRoute />}>
                            <Route path="/" element={<Layout />}>
                                <Route index element={<MainContent />} />
                                {/* مسیرهای اضافی */}
                            </Route>
                        </Route>

                        {/* مسیرهای نامشخص */}
                        <Route path="*" element={<Navigate to="/" />} />
                    </Routes>
                </div>
            </Router>
        </AuthProvider>
    );
};

export default App;
