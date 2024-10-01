// src/components/Profile.js
import React, { useContext } from 'react';
import { Dropdown, Image } from 'react-bootstrap';
import profileImg from '../assets/profile-major.svg';
import { AuthContext } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';

const Profile = () => {
    const { user, logout } = useContext(AuthContext);
    const navigate = useNavigate();

    const handleSignOut = () => {
        logout();
        navigate('/auth'); // هدایت به صفحه ورود پس از خروج
    };

    return (
        <div className="header-right">
            <Dropdown className="float-end" align="end">
                <Dropdown.Toggle variant="light" id="dropdown-basic" className="d-flex align-items-center border-0 bg-transparent">
                    <div className="d-flex align-items-center">
                        <div className="me-2">
                            {user ? (
                                <small className="text-dark">{user.userName}</small>
                            ) : (
                                <span className="text-muted">Guest</span>
                            )}
                        </div>
                        <Image src={profileImg} alt="Profile" className="profile-img rounded-circle" width={40} height={40} />
                    </div>
                </Dropdown.Toggle>

                <Dropdown.Menu>
                    <Dropdown.Item href="/profile">Profile</Dropdown.Item>
                    <Dropdown.Item href="/settings">Settings</Dropdown.Item>
                    <Dropdown.Divider />
                    <Dropdown.Item onClick={handleSignOut}>Sign-out</Dropdown.Item>
                </Dropdown.Menu>
            </Dropdown>
        </div>
    );
};

export default Profile;
