/* =====================================================
   GREENPULSE AI DASHBOARD
   Live Supabase Data + Chart.js
===================================================== */


/* API PATHS */

const API = {
    latest: "api/latest.php",
    history: "api/history.php",
    summary: "api/summary.php"
};


/* CHART OBJECTS */

let powerChart = null;
let voltageChart = null;
let currentChart = null;
let temperatureChart = null;


/* =====================================================
   CHART CONFIGURATION
===================================================== */

function createChart(
    canvasId,
    label,
    unit
) {

    const canvas =
        document.getElementById(canvasId);

    if (!canvas) {
        return null;
    }

    return new Chart(canvas, {

        type: "line",

        data: {

            labels: [],

            datasets: [

                {
                    label: label,

                    data: [],

                    borderWidth: 2,

                    pointRadius: 2,

                    pointHoverRadius: 5,

                    tension: 0.35,

                    fill: true,

                    backgroundColor:
                        "rgba(77,163,255,0.08)",

                    borderColor:
                        "#4da3ff"
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                intersect: false,
                mode: "index"
            },

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {

                    callbacks: {

                        label: function(context) {

                            return (
                                " " +
                                context.parsed.y +
                                " " +
                                unit
                            );

                        }

                    }

                }

            },

            scales: {

                x: {

                    grid: {
                        display: false
                    },

                    ticks: {
                        color: "#657990",
                        maxTicksLimit: 8,
                        font: {
                            size: 9
                        }
                    }

                },

                y: {

                    grid: {
                        color:
                            "rgba(255,255,255,0.05)"
                    },

                    ticks: {
                        color: "#657990",
                        font: {
                            size: 9
                        }
                    }

                }

            }

        }

    });

}


/* =====================================================
   INITIALIZE CHARTS
===================================================== */

function initializeCharts() {

    powerChart =
        createChart(
            "powerChart",
            "Power",
            "W"
        );

    voltageChart =
        createChart(
            "voltageChart",
            "Voltage",
            "V"
        );

    currentChart =
        createChart(
            "currentChart",
            "Current",
            "A"
        );

    temperatureChart =
        createChart(
            "temperatureChart",
            "Temperature",
            "°C"
        );
}


/* =====================================================
   FORMAT TIME
===================================================== */

function formatTime(dateString) {

    if (!dateString) {
        return "--";
    }

    const date =
        new Date(dateString);

    if (isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleTimeString(
        [],
        {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        }
    );
}


/* =====================================================
   LOAD LATEST DATA
===================================================== */

async function loadLatest() {

    try {

        const response =
            await fetch(
                API.latest +
                "?t=" +
                Date.now()
            );

        if (!response.ok) {
            throw new Error(
                "HTTP " +
                response.status
            );
        }

        const result =
            await response.json();


        if (!result.success) {

            throw new Error(
                result.message ||
                "API returned an error"
            );

        }


        const data =
            result.data;


        updateDashboard(data);


        setConnection(true);


    }

    catch (error) {

        console.error(
            "Latest data error:",
            error
        );

        setConnection(false);

    }

}


/* =====================================================
   UPDATE DASHBOARD
===================================================== */

function updateDashboard(data) {

    if (!data) {
        return;
    }


    const voltage =
        Number(data.voltage) || 0;

    const current =
        Number(data.current) || 0;

    const temperature =
        Number(data.temperature) || 0;

    const power =
        Number(data.power) ||
        voltage * current;

    const confidence =
        Number(data.ai_confidence) || 0;

    const status =
        String(
            data.status ||
            "UNKNOWN"
        );

    const fault =
        String(
            data.fault_type ||
            data.fault ||
            "NOMINAL"
        );


    /* LIVE CARDS */

    setText(
        "voltage",
        voltage.toFixed(2)
    );

    setText(
        "current",
        current.toFixed(2)
    );

    setText(
        "power",
        power.toFixed(2)
    );

    setText(
        "temperature",
        temperature.toFixed(2)
    );


    /* STATUS */

    updateStatus(
        status,
        fault
    );


    /* AI */

    setText(
        "confidence",
        confidence.toFixed(0)
    );

    const confidenceBar =
        document.getElementById(
            "confidenceBar"
        );

    if (confidenceBar) {

        confidenceBar.style.width =
            Math.min(
                Math.max(
                    confidence,
                    0
                ),
                100
            ) + "%";

    }


    /* FAULT PANEL */

    updateFaultPanel(
        status,
        fault,
        voltage,
        current,
        temperature,
        power
    );


    /* UPDATE TIME */

    setText(
        "lastUpdate",
        formatTime(
            data.created_at
        )
    );

}


/* =====================================================
   UPDATE STATUS
===================================================== */

function updateStatus(
    status,
    fault
) {

    const statusElement =
        document.getElementById(
            "status"
        );

    const faultElement =
        document.getElementById(
            "fault"
        );

    const icon =
        document.getElementById(
            "statusIcon"
        );


    if (!statusElement) {
        return;
    }


    statusElement.textContent =
        status;


    if (status === "NORMAL") {

        statusElement.style.color =
            "#35e28a";

        icon.textContent = "✓";

        icon.style.color =
            "#35e28a";

        icon.style.background =
            "rgba(53,226,138,0.12)";

        faultElement.textContent =
            "System operating within normal parameters.";

    }

    else if (status === "IDLE") {

        statusElement.style.color =
            "#ffc857";

        icon.textContent = "○";

        icon.style.color =
            "#ffc857";

        icon.style.background =
            "rgba(255,200,87,0.12)";

        faultElement.textContent =
            fault;

    }

    else {

        statusElement.style.color =
            "#ff5d6c";

        icon.textContent = "!";

        icon.style.color =
            "#ff5d6c";

        icon.style.background =
            "rgba(255,93,108,0.12)";

        faultElement.textContent =
            fault;

    }

}


/* =====================================================
   FAULT PANEL
===================================================== */

function updateFaultPanel(
    status,
    fault,
    voltage,
    current,
    temperature,
    power
) {

    const indicator =
        document.getElementById(
            "faultIndicator"
        );

    const title =
        document.getElementById(
            "faultTitle"
        );

    const description =
        document.getElementById(
            "faultDescription"
        );


    setText(
        "faultVoltage",
        voltage.toFixed(2) + " V"
    );

    setText(
        "faultCurrent",
        current.toFixed(2) + " A"
    );

    setText(
        "faultTemperature",
        temperature.toFixed(2) + " °C"
    );

    setText(
        "faultPower",
        power.toFixed(2) + " W"
    );


    indicator.className =
        "fault-indicator";


    if (status === "NORMAL") {

        indicator.classList.add(
            "normal"
        );

        indicator.textContent =
            "✓";

        title.textContent =
            "System Normal";

        description.textContent =
            "No abnormal operating condition detected.";

    }

    else if (status === "IDLE") {

        indicator.classList.add(
            "warning"
        );

        indicator.textContent =
            "○";

        title.textContent =
            "Low Generation";

        description.textContent =
            fault;

    }

    else {

        indicator.classList.add(
            "fault"
        );

        indicator.textContent =
            "!";

        title.textContent =
            "Fault Detected";

        description.textContent =
            fault;

    }

}


/* =====================================================
   LOAD HISTORY
===================================================== */

async function loadHistory() {

    try {

        const response =
            await fetch(
                API.history +
                "?t=" +
                Date.now()
            );


        if (!response.ok) {
            throw new Error(
                "History HTTP " +
                response.status
            );
        }


        const result =
            await response.json();


        if (!result.success) {

            throw new Error(
                result.message ||
                "History API error"
            );

        }


        let records =
            result.data ||
            result.records ||
            [];


        if (!Array.isArray(records)) {
            records = [];
        }


        updateHistoryTable(
            records
        );

        updateCharts(
            records
        );

    }

    catch (error) {

        console.error(
            "History error:",
            error
        );

        const body =
            document.getElementById(
                "historyBody"
            );

        if (body) {

            body.innerHTML =
                `<tr>
                    <td colspan="8"
                        class="loading">
                        Unable to load historical data
                    </td>
                </tr>`;

        }

    }

}


/* =====================================================
   HISTORY TABLE
===================================================== */

function updateHistoryTable(
    records
) {

    const body =
        document.getElementById(
            "historyBody"
        );


    if (!body) {
        return;
    }


    if (records.length === 0) {

        body.innerHTML =
            `<tr>
                <td colspan="8"
                    class="loading">
                    No historical records found
                </td>
            </tr>`;

        return;

    }


    /* Show newest first */

    records =
        [...records]
        .sort(
            (a, b) =>
                Number(b.id || 0) -
                Number(a.id || 0)
        )
        .slice(0, 20);


    body.innerHTML =
        records.map(
            record => {

                const status =
                    String(
                        record.status ||
                        "UNKNOWN"
                    );


                let statusClass =
                    "status-normal";


                if (status === "FAULT") {

                    statusClass =
                        "status-fault";

                }

                else if (status === "IDLE") {

                    statusClass =
                        "status-idle";

                }


                return `

                <tr>

                    <td>
                        #${escapeHtml(record.id)}
                    </td>

                    <td>
                        ${escapeHtml(
                            formatTime(
                                record.created_at
                            )
                        )}
                    </td>

                    <td>
                        ${Number(
                            record.voltage || 0
                        ).toFixed(2)} V
                    </td>

                    <td>
                        ${Number(
                            record.current || 0
                        ).toFixed(2)} A
                    </td>

                    <td>
                        ${Number(
                            record.power || 0
                        ).toFixed(2)} W
                    </td>

                    <td>
                        ${Number(
                            record.temperature || 0
                        ).toFixed(2)} °C
                    </td>

                    <td>

                        <span class="status-badge ${statusClass}">
                            ${escapeHtml(status)}
                        </span>

                    </td>

                    <td>
                        ${escapeHtml(
                            record.fault_type ||
                            record.fault ||
                            "NOMINAL"
                        )}
                    </td>

                </tr>

                `;

            }
        ).join("");

}


/* =====================================================
   UPDATE CHARTS
===================================================== */

function updateCharts(records) {

    if (!records || records.length === 0) {
        return;
    }


    /* Chronological order */

    const data =
        [...records]
        .sort(
            (a, b) =>
                new Date(a.created_at) -
                new Date(b.created_at)
        )
        .slice(-50);


    const labels =
        data.map(
            item =>
                formatTime(
                    item.created_at
                )
        );


    const voltage =
        data.map(
            item =>
                Number(
                    item.voltage || 0
                )
        );


    const current =
        data.map(
            item =>
                Number(
                    item.current || 0
                )
        );


    const power =
        data.map(
            item =>
                Number(
                    item.power ||
                    (
                        Number(item.voltage || 0) *
                        Number(item.current || 0)
                    )
                )
        );


    const temperature =
        data.map(
            item =>
                Number(
                    item.temperature || 0
                )
        );


    updateChart(
        powerChart,
        labels,
        power
    );


    updateChart(
        voltageChart,
        labels,
        voltage
    );


    updateChart(
        currentChart,
        labels,
        current
    );


    updateChart(
        temperatureChart,
        labels,
        temperature
    );

}


/* =====================================================
   UPDATE SINGLE CHART
===================================================== */

function updateChart(
    chart,
    labels,
    data
) {

    if (!chart) {
        return;
    }


    chart.data.labels =
        labels;

    chart.data.datasets[0].data =
        data;

    chart.update(
        "none"
    );

}


/* =====================================================
   CONNECTION STATUS
===================================================== */

function setConnection(
    connected
) {

    const element =
        document.getElementById(
            "connectionStatus"
        );


    if (!element) {
        return;
    }


    if (connected) {

        element.className =
            "connection online";

        element.textContent =
            "● API Connected";

    }

    else {

        element.className =
            "connection offline";

        element.textContent =
            "● API Offline";

    }

}


/* =====================================================
   HELPER
===================================================== */

function setText(
    id,
    value
) {

    const element =
        document.getElementById(id);

    if (element) {
        element.textContent =
            value;
    }

}


/* =====================================================
   HTML ESCAPE
===================================================== */

function escapeHtml(value) {

    return String(value ?? "")
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );

}


/* =====================================================
   REFRESH BUTTON
===================================================== */

document
    .getElementById(
        "refreshHistory"
    )
    ?.addEventListener(
        "click",
        loadHistory
    );


/* =====================================================
   START DASHBOARD
===================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function() {

        initializeCharts();

        loadLatest();

        loadHistory();

        /*
           Refresh every 10 seconds
        */

        setInterval(
            loadLatest,
            10000
        );

        setInterval(
            loadHistory,
            10000
        );

    }
);