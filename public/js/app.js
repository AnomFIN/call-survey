const actions = {
  call: {
    label: "Aloita soittaminen",
    description:
      "Avataan Twilio Voice -virtauksen hallintapaneeli ja varmistetaan, että DTMF 1/2 -reititys on valmis.",
    href: "https://console.twilio.com/",
  },
  sms: {
    label: "Lähetä SMS",
    description: "Luo uusi tekstiviestikampanja ULTRALIGHT-kohderyhmälle.",
    href: "https://console.twilio.com/us1/develop/sms/sms-messages",
  },
  whatsapp: {
    label: "Lähetä WhatsApp",
    description: "Aktivoi WhatsApp-flow ja esikatsele viimeisin template.",
    href: "https://console.twilio.com/us1/develop/whatsapp/sandbox",
  },
};

function animateCounters() {
  const counters = [
    { id: "app-stat-live", start: 18, end: 28, suffix: "" },
    { id: "app-stat-messages", start: 1182, end: 1482, suffix: "" },
    { id: "app-stat-nps", start: 52, end: 72, suffix: "" },
  ];

  counters.forEach(({ id, start, end, suffix }) => {
    const el = document.getElementById(id);
    if (!el) return;
    let current = start;
    const step = Math.max(1, Math.round((end - start) / 30));
    const interval = window.setInterval(() => {
      current += step;
      if (current >= end) {
        current = end;
        window.clearInterval(interval);
      }
      el.textContent = `${current.toLocaleString("fi-FI")}${suffix}`;
    }, 48);
  });
}

function bindCtas() {
  document.querySelectorAll(".app-cta").forEach((button) => {
    const action = actions[button.dataset.action];
    if (!action) return;
    button.setAttribute("aria-label", `${action.label} – ${action.description}`);
    button.addEventListener("click", () => {
      window.open(action.href, "_blank", "noopener,noreferrer");
    });
  });
}

window.addEventListener("DOMContentLoaded", () => {
  bindCtas();
  animateCounters();
});
