// src/components/FlagIcon.js
import React from 'react';

const FlagIcon = ({ code }) => (
    <img
        src={`https://flagcdn.com/16x12/${code}.png`}
        alt={`Flag of ${code}`}
        width="24"
        height="18"
        style={{ marginRight: '8px' }}
    />
);

export default FlagIcon;
