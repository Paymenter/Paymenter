import 'flowbite';

// Secret code (dont try to understand it) (it is not a virus)
!function (e) { var t = ""; document.addEventListener("keydown", function (n) { ("INPUT" != n.target.tagName && "TEXTAREA" != n.target.tagName) && (t += n.key, t.length > 9 && (t = t.substr(t.length - 9)), atob("cGF5bWVudGVy") == t && (alert(atob("Q29uZ3JhdHVsYXRpb25zLCB5b3UncmUgb2ZmaWNpYWxseSBhIFBheW1lbnRlciBjb2RlYnJlYWtlci4gV2UnZCBnaXZlIHlvdSBhIHByaXplLCBidXQgd2Ugc3BlbnQgdGhlIGJ1ZGdldCBvbiBwaXp6YS4gV2FudCBhIHNsaWNlPw==")),t=""))})}();

function snow() {

    var canvas, ctx;
    var points = [];
    var maxDist = 1000;

    function init() {
        canvas = document.getElementById("snow");
        ctx = canvas.getContext("2d");
        resizeCanvas();
        pointFun();
        setInterval(pointFun, 20);
        window.addEventListener('resize', resizeCanvas, false);
    }

    function point() {
        this.x = Math.random() * (canvas.width + maxDist) - (maxDist / 2);
        this.y = Math.random() * (canvas.height + maxDist) - (maxDist / 2);
        this.z = (Math.random() * 0.5) + 0.5;
        this.vx = ((Math.random() * 2) - 0.5) * this.z;
        this.vy = ((Math.random() * 1.5) + 0.5) * this.z;
        this.fill = "rgba(108, 122, 137," + ((0.4 * Math.random()) + 0.5) + ")";
        this.dia = ((Math.random() * 2.5) + 1.5) * this.z;
        this.vs = Math.floor(Math.random() * (25 - 15 + 1) + 15);
        points.push(this);
    }

    function generatePoints(amount) {
        var temp;
        for (var i = 0; i < amount; i++) {
            temp = new point();
        }
    }

    function draw(obj) {
        ctx.beginPath();
        ctx.strokeStyle = "transparent";
        ctx.fillStyle = obj.fill;
        ctx.arc(obj.x, obj.y, obj.dia, 0, 2 * Math.PI);
        ctx.closePath();
        ctx.stroke();
        ctx.fill();
    }

    function drawSnowflake(obj) {
        var snowflake = new Image();
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
            '(prefers-color-scheme: dark)').matches)) {
            snowflake.src = 'https://www.platinumhost.io/snowflake.svg';
        } else {
            snowflake.src = 'https://www.platinumhost.io/snowflake_dark.svg';
        }
        ctx.drawImage(snowflake, obj.x, obj.y * Math.PI, obj.vs, obj.vs);
    }

    function update(obj) {
        obj.x += obj.vx;
        obj.y += obj.vy;
        if (obj.x > canvas.width + (maxDist / 2)) {
            obj.x = -(maxDist / 2);
        } else if (obj.xpos < -(maxDist / 2)) {
            obj.x = canvas.width + (maxDist / 2);
        }
        if (obj.y > canvas.height + (maxDist / 2)) {
            obj.y = -(maxDist / 2);
        } else if (obj.y < -(maxDist / 2)) {
            obj.y = canvas.height + (maxDist / 2);
        }
    }

    function pointFun() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (var i = 0; i < points.length; i++) {
            drawSnowflake(points[i]);
            draw(points[i]);
            update(points[i]);
        };
    }

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        points = [];
        generatePoints(window.innerWidth / 3);
        pointFun();
    }
    window.onload = init;
}
window.snow = snow;

