// src/context/AuthContext.js

import React, { createContext, useState, useEffect } from 'react';
import api from '../utils/api';

export const AuthContext = createContext({
    user: null,
    isAuthenticated: false,
    loading: true,
    login: () => {},
    logout: () => {},
});

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [loading, setLoading] = useState(true);

    const fetchUserData = async (token) => {
        try {
            const response = await api.get('/user', {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            console.log('AuthContext.js - API response:', response.data); // برای عیب‌یابی
            if (response.status === 200 && response.data.user) {
                setUser(response.data.user); // اصلاح شده
                setIsAuthenticated(true);
            }
        } catch (error) {
            console.error('Error fetching user:', error);
            localStorage.removeItem('token');
            setUser(null);
            setIsAuthenticated(false);
        }
    };

    useEffect(() => {
        const fetchUser = async () => {
            const token = localStorage.getItem('token');
            if (token) {
                await fetchUserData(token);
            }
            setLoading(false);
        };

        fetchUser();
    }, []);

    const login = (token) => {
        localStorage.setItem('token', token);
        setIsAuthenticated(true);
        fetchUserData(token);
    };

    const logout = () => {
        localStorage.removeItem('token');
        setUser(null);
        setIsAuthenticated(false);
    };

    return (
        <AuthContext.Provider value={{ user, isAuthenticated, loading, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};
