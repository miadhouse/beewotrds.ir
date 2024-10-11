// src/components/Profile.js
import React, { useContext } from 'react';
import { Dropdown, Image, Spinner } from 'react-bootstrap';
import profileImg from '../assets/profile-major.svg';
import { AuthContext } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const Profile = () => {
    const { t } = useTranslation();
    const { user, logout, loading } = useContext(AuthContext);
    const navigate = useNavigate();

    console.log('Current user:', user); // برای عیب‌یابی

    const handleSignOut = () => {
        logout();
        navigate('/auth'); // هدایت به صفحه ورود پس از خروج
    };

    if (loading) {
        // نمایش Spinner در زمان بارگذاری
        return (
            <div className="header-right">
                <Spinner animation="border" size="sm" />
            </div>
        );
    }

    return (
        <div className="header-right">
            <Dropdown className="profileDivChild" align="end">
                <Dropdown.Toggle variant="light" id="dropdown-basic" className="d-flex align-items-center border-0 bg-transparent">
                    <div className="d-flex align-items-center">
                        <div className="profileEmail">
                            {user ? (
                                <small className="text-dark">{user.name}</small>
                            ) : (
                                <span className="text-muted">{t('Guest')}</span>
                            )}
                        </div>
                        <Image src={profileImg} alt="Profile" className="profile-img rounded-circle" width={40} height={40} />
                    </div>
                </Dropdown.Toggle>

                <Dropdown.Menu>
                    <Dropdown.Item href="/profile">{t('Profile')}</Dropdown.Item>
                    <Dropdown.Item href="/settings">{t('Settings')}</Dropdown.Item>
                    <Dropdown.Divider />
                    <Dropdown.Item onClick={handleSignOut}>{t('Sign-out')}</Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>
        </div>
    );
};

export default Profile;
