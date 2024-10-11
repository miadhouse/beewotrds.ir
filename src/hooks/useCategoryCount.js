// src/hooks/useCategoryCount.js
import { useState, useEffect, useContext } from 'react';
import api from '../utils/api'; // استفاده از api instance
import { AuthContext } from '../context/AuthContext';

const useCategoryCount = () => {
    const [count, setCount] = useState(0);
    const [loading, setLoading] = useState(true);
    const { isAuthenticated } = useContext(AuthContext);

    useEffect(() => {
        const fetchCount = async () => {
            if (isAuthenticated) {
                const token = localStorage.getItem('token');
                try {
                    const response = await api.get('/categories/count', { // استفاده از مسیر صحیح
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });
                    if (response.status === 200) {
                        setCount(response.data.count);
                    }
                } catch (error) {
                    console.error('Error fetching category count:', error);
                }
            }
            setLoading(false);
        };

        fetchCount();
    }, [isAuthenticated]);

    return { count, loading };
};

export default useCategoryCount;
