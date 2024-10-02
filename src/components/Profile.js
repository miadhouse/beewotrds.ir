// src/components/Profile.js
import React, { useContext } from 'react';
import { Dropdown, Image } from 'react-bootstrap';
import profileImg from '../assets/profile-major.svg';
import { AuthContext } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const Profile = () => {
        const { t, i18n } = useTranslation();

    const { user, logout } = useContext(AuthContext);
    const navigate = useNavigate();

    const handleSignOut = () => {
        logout();
        navigate('/auth'); // هدایت به صفحه ورود پس از خروج
    };

    return (
        <div className="header-right">
            <Dropdown className="profileDivChild" align="end">
                <Dropdown.Toggle variant="light" id="dropdown-basic" className="d-flex align-items-center border-0 bg-transparent">
                    <div className="d-flex align-items-center">
                        <div className="profileEmail">
                            {user ? (
                                <small className="text-dark">{user.userName}</small>
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
