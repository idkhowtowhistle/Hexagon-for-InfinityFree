const clickerButton = document.querySelector('[data-clicker-button]');
const clickerCount = document.querySelector('[data-clicker-count]');
const hexIcon = document.querySelector('[data-hex-icon]');
const biteAudio = document.querySelector('[data-bite-audio]');
const doneAudio = document.querySelector('[data-done-audio]');
const emote = document.querySelector('[data-emote]');

const emoticons = [
    '(^_^)',
    '(o_o)',
    '(>_<)',
    '(-_-)',
    '(0_0)',
    '(#_#)',
    '(^-^)',
    '(1_1)',
    '(._.)',
];

let biteCount = 0;

async function updateClicker(method = 'GET') {
    if (!clickerCount) return;

    const response = await fetch('api/clicker.php', { method });
    const payload = await response.json();

    if (payload && payload.data) {
        clickerCount.textContent = payload.data.clicker;
    }
}

if (clickerButton && hexIcon) {
    clickerButton.addEventListener('click', async () => {
        hexIcon.classList.add('boop');
        window.setTimeout(() => hexIcon.classList.remove('boop'), 900);

        biteCount = biteCount >= 3 ? 0 : biteCount + 1;
        hexIcon.src = biteCount === 0 ? 'assets/hexagon512.png' : `assets/hexabite/${biteCount}.png`;

        if (biteCount < 3 && biteAudio) {
            biteAudio.currentTime = 0;
            biteAudio.play().catch(() => {});
        } else if (doneAudio) {
            doneAudio.currentTime = 0;
            doneAudio.play().catch(() => {});
        }

        if (emote) {
            emote.textContent = emoticons[Math.floor(Math.random() * emoticons.length)];
        }

        await updateClicker('POST');
    });
}

if (clickerCount) {
    window.setInterval(() => updateClicker('GET'), 5000);
}
