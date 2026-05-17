// InkPress Author Portal — Main JS

// ===== Bar Chart (vanilla canvas, no library needed) =====
function renderBarChart(canvasId, labels, data, label) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.offsetWidth || 600;
    const H = canvas.height || 160;
    canvas.width = W;

    const accent = '#c8a96e';
    const textColor = '#9090b0';
    const gridColor = '#2a2a38';
    const pad = { top: 20, right: 20, bottom: 40, left: 45 };

    const max = Math.max(...data, 1);
    const chartW = W - pad.left - pad.right;
    const chartH = H - pad.top - pad.bottom;
    const barW = Math.max(4, chartW / data.length * 0.6);
    const gap = chartW / data.length;

    ctx.clearRect(0, 0, W, H);
    ctx.fillStyle = '#16161c';
    ctx.fillRect(0, 0, W, H);

    // Grid lines
    ctx.strokeStyle = gridColor;
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
        const y = pad.top + chartH - (i / 4) * chartH;
        ctx.beginPath();
        ctx.moveTo(pad.left, y);
        ctx.lineTo(W - pad.right, y);
        ctx.stroke();
        ctx.fillStyle = textColor;
        ctx.font = '10px DM Sans, sans-serif';
        ctx.textAlign = 'right';
        ctx.fillText(Math.round(max * i / 4), pad.left - 6, y + 4);
    }

    // Bars
    data.forEach((val, i) => {
        const x = pad.left + i * gap + gap / 2 - barW / 2;
        const barH = val > 0 ? (val / max) * chartH : 2;
        const y = pad.top + chartH - barH;

        const grad = ctx.createLinearGradient(0, y, 0, pad.top + chartH);
        grad.addColorStop(0, accent);
        grad.addColorStop(1, 'rgba(200,169,110,0.2)');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.roundRect(x, y, barW, barH, 3);
        ctx.fill();

        // X label
        if (labels[i]) {
            ctx.fillStyle = textColor;
            ctx.font = '9px DM Sans, sans-serif';
            ctx.textAlign = 'center';
            const lbl = labels[i].length > 5 ? labels[i].slice(5) : labels[i];
            ctx.fillText(lbl, x + barW / 2, H - pad.bottom + 14);
        }
    });

    // Label
    ctx.fillStyle = accent;
    ctx.font = '11px DM Sans, sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText(label, pad.left, pad.top - 6);
}

// ===== Flash auto-dismiss =====
document.addEventListener('DOMContentLoaded', function () {
    const flashes = document.querySelectorAll('.flash');
    flashes.forEach(f => {
        setTimeout(() => {
            f.style.opacity = '0';
            f.style.transition = 'opacity .5s';
            setTimeout(() => f.remove(), 500);
        }, 4000);
    });
});
