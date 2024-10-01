// src/components/ChartComponent.js

import React, {useRef } from 'react';
import { Line } from 'react-chartjs-2';
import {
    Chart as ChartJS,           // Import Chart as ChartJS to avoid naming conflicts
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

// Register the necessary components with ChartJS
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const ChartComponent = ({ id, title, dataSetKey, dataSet }) => {
    const chartRef = useRef(null);

    // Define the data based on the selected timeframe
    const chartData = {
        labels: dataSet[dataSetKey].map(item => item.name),
        datasets: [
            {
                label: 'بازدیدها',
                data: dataSet[dataSetKey].map(item => item.view),
                fill: true,
                backgroundColor: (context) => {
                    const chartArea = context.chart.chartArea;
                    if (!chartArea) {
                        // This case happens on initial chart load
                        return null;
                    }
                    const ctx = context.chart.ctx;
                    const gradient = ctx.createLinearGradient(0, 0, 0, chartArea.bottom);
                    gradient.addColorStop(0.3, 'rgba(136, 132, 216, 0.4)'); // #8884d8
                    gradient.addColorStop(0.75, 'rgba(255, 155, 255, 0.3)'); // #ff9bff81
                    gradient.addColorStop(0.95, 'rgba(255, 255, 255, 0.2)'); // #FFFFFF
                    return gradient;
                },
                borderColor: 'rgba(136,132,216,0.32)',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 0, // Remove data points
            }
        ]
    };

    // Define the chart options
    const options = {
        responsive: true,
        scales: {
            x: {
                display: false, // Hide X-axis
                grid: {
                    display: false, // Hide X-axis grid lines
                }
            },
            y: {
                display: false, // Hide Y-axis
                grid: {
                    display: false, // Hide Y-axis grid lines
                }
            }
        },
        plugins: {
            tooltip: {
                enabled: false, // Disable tooltips
            },
            legend: {
                display: false, // Hide legend
            }
        },
        elements: {
            line: {
                borderWidth: 2, // Set line thickness
            }
        }
    };

    return (
        <div>
            <Line ref={chartRef} data={chartData} options={options} />
            <small>{title}</small>
        </div>
    );
};

export default ChartComponent;
