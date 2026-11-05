/**
 * Generates the static SVG artwork used across the portfolio.
 * Run with: node tools/generate-assets.mjs
 */
import { writeFileSync, mkdirSync } from "node:fs";
import { join } from "node:path";

const root = new URL("../", import.meta.url).pathname.replace(/^\/([A-Za-z]:)/, "$1");
const out = join(root, "assets", "images");
mkdirSync(join(out, "projects"), { recursive: true });

const sans = "'Segoe UI', 'Helvetica Neue', Arial, sans-serif";
const serif = "Georgia, 'Times New Roman', serif";

const shell = (id, w, h, body, extraDefs = "") => `<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}" viewBox="0 0 ${w} ${h}" fill="none" role="img">
  <defs>
    <filter id="grain-${id}" x="0" y="0" width="100%" height="100%">
      <feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" stitchTiles="stitch"/>
      <feColorMatrix type="saturate" values="0"/>
    </filter>
    <filter id="shadow-${id}" x="-30%" y="-30%" width="160%" height="160%">
      <feDropShadow dx="0" dy="28" stdDeviation="34" flood-color="#000" flood-opacity=".35"/>
    </filter>
    ${extraDefs}
  </defs>
  ${body}
  <rect width="${w}" height="${h}" filter="url(#grain-${id})" opacity=".045" style="mix-blend-mode:overlay"/>
</svg>`;

const browser = (id, x, y, w, h, bar, content) => `
  <g filter="url(#shadow-${id})">
    <rect x="${x}" y="${y}" width="${w}" height="${h}" rx="20" fill="${bar}"/>
  </g>
  <rect x="${x}" y="${y}" width="${w}" height="${h}" rx="20" fill="none" stroke="#fff" stroke-opacity=".12"/>
  <path d="M${x} ${y + 44}h${w}" stroke="#fff" stroke-opacity=".10"/>
  <circle cx="${x + 26}" cy="${y + 22}" r="5" fill="#FF6157" fill-opacity=".85"/>
  <circle cx="${x + 44}" cy="${y + 22}" r="5" fill="#FEBC2E" fill-opacity=".85"/>
  <circle cx="${x + 62}" cy="${y + 22}" r="5" fill="#28C840" fill-opacity=".85"/>
  <rect x="${x + w / 2 - 150}" y="${y + 13}" width="300" height="18" rx="9" fill="#fff" fill-opacity=".07"/>
  <text x="${x + w / 2}" y="${y + 26}" text-anchor="middle" fill="#fff" fill-opacity=".36" font-family="${sans}" font-size="9.5" letter-spacing=".8">lumen.studio</text>
  <g transform="translate(${x}, ${y + 44})">${content}</g>`;

const pill = (x, y, w, h, fill, label, color, size = 11, weight = 600) => `
  <rect x="${x}" y="${y}" width="${w}" height="${h}" rx="${h / 2}" fill="${fill}"/>
  <text x="${x + w / 2}" y="${y + h / 2 + size * .36}" text-anchor="middle" fill="${color}" font-family="${sans}" font-size="${size}" font-weight="${weight}">${label}</text>`;

const line = (x, y, w, color, opacity = 1, h = 6, r = 3) =>
  `<rect x="${x}" y="${y}" width="${w}" height="${h}" rx="${r}" fill="${color}" fill-opacity="${opacity}"/>`;

/* ------------------------------------------------------------------ */
/* 01 — Lumen Studio (light, editorial WordPress site)                 */
/* ------------------------------------------------------------------ */
const lumen = shell("lumen", 1200, 800, `
  <rect width="1200" height="800" fill="#EFE8DD"/>
  <circle cx="140" cy="80" r="340" fill="#C9B5FF" opacity=".5"/>
  <circle cx="1080" cy="700" r="360" fill="#F6C9A8" opacity=".55"/>
  <circle cx="1020" cy="80" r="220" fill="#BEE8E4" opacity=".35"/>
  ${browser("lumen", 60, 58, 1080, 684, "#FBF7F0", `
    <g font-family="${sans}">
      <text x="52" y="54" fill="#1A1712" font-family="${serif}" font-size="25" letter-spacing="-0.5">LUMEN<tspan font-family="${sans}" font-size="10" dy="-12">®</tspan></text>
      <g fill="#1A1712" opacity=".55" font-size="12" font-weight="500">
        <text x="450" y="54">Work</text><text x="510" y="54">Studio</text><text x="577" y="54">Journal</text>
      </g>
      <rect x="830" y="28" width="130" height="38" rx="19" fill="#1A1712"/>
      <text x="895" y="52" text-anchor="middle" fill="#FBF7F0" font-size="11" font-weight="600">Start a project</text>
      <text x="52" y="140" fill="#8A7A67" font-size="10.5" font-weight="600" letter-spacing="2.4">CREATIVE STUDIO — EST. 2019</text>
      <text x="52" y="224" fill="#17130E" font-family="${serif}" font-size="72" letter-spacing="-2.6">Brands that</text>
      <text x="52" y="300" fill="#17130E" font-family="${serif}" font-size="72" letter-spacing="-2.6">feel <tspan fill="#6D4AFF" font-style="italic">alive.</tspan></text>
      <text x="54" y="352" fill="#6B5B49" font-size="14" font-family="${sans}">We build identities, websites and moments</text>
      <text x="54" y="374" fill="#6B5B49" font-size="14" font-family="${sans}">that make people stop scrolling.</text>
      <rect x="52" y="414" width="164" height="48" rx="24" fill="#6D4AFF"/>
      <text x="134" y="443" text-anchor="middle" fill="#fff" font-size="12" font-weight="600">Explore our work</text>
      <text x="248" y="443" fill="#17130E" font-size="12" font-weight="600">View showreel ↗</text>
      <g>
        <rect x="52" y="530" width="170" height="92" rx="14" fill="#fff" fill-opacity=".7" stroke="#D9CDBC"/>
        <text x="72" y="566" fill="#17130E" font-family="${serif}" font-size="28">48</text>
        <text x="72" y="590" fill="#8A7A67" font-size="9.5" letter-spacing="1.1">PROJECTS SHIPPED</text>
        <rect x="238" y="530" width="170" height="92" rx="14" fill="#fff" fill-opacity=".7" stroke="#D9CDBC"/>
        <text x="258" y="566" fill="#17130E" font-family="${serif}" font-size="28">12</text>
        <text x="258" y="590" fill="#8A7A67" font-size="9.5" letter-spacing="1.1">AWARDS WON</text>
      </g>
      <g>
        <path d="M738 92c30 18 32 62 2 82-10 7-24 10-40 9V92h38Z" fill="#D9C7FF"/>
        <rect x="700" y="92" width="290" height="340" rx="145" fill="#C9B3FF"/>
        <rect x="700" y="92" width="290" height="340" rx="145" fill="url(#lumen-arch)"/>
        <circle cx="845" cy="215" r="78" fill="#17130E" opacity=".9"/>
        <path d="M812 239c14-42 52-48 66-48 0 34-14 66-52 72-6 1-12-8-14-24Z" fill="#EFE8DD"/>
        <path d="M824 249c16-18 36-26 50-28" stroke="#17130E" stroke-width="2" stroke-linecap="round"/>
        <circle cx="845" cy="215" r="78" fill="none" stroke="#fff" stroke-opacity=".35" stroke-dasharray="3 8"/>
        <rect x="730" y="364" width="230" height="42" rx="21" fill="#FBF7F0" fill-opacity=".92"/>
        <circle cx="752" cy="385" r="10" fill="#6D4AFF"/>
        <text x="770" y="381" fill="#17130E" font-size="10" font-weight="600">Award-winning studio</text>
        <text x="770" y="395" fill="#8A7A67" font-size="8.5">Design × Development</text>
        <text x="1002" y="470" fill="#6D4AFF" font-family="${serif}" font-size="17" font-style="italic" transform="rotate(-90 1002 470)">since 2019</text>
      </g>
      <path d="M52 656h976" stroke="#D9CDBC"/>
      <text x="52" y="682" fill="#8A7A67" font-size="9.5" letter-spacing="2">BRAND STRATEGY</text>
      <text x="246" y="682" fill="#8A7A67" font-size="9.5" letter-spacing="2">WEB DESIGN</text>
      <text x="430" y="682" fill="#8A7A67" font-size="9.5" letter-spacing="2">WORDPRESS</text>
      <text x="620" y="682" fill="#8A7A67" font-size="9.5" letter-spacing="2">ART DIRECTION</text>
      <text x="880" y="682" fill="#17130E" font-size="10" font-weight="600">hello@lumen.studio</text>
    </g>
  `)}
`, `
    <linearGradient id="lumen-arch" x1="700" y1="92" x2="990" y2="432" gradientUnits="userSpaceOnUse">
      <stop stop-color="#E8D9FF"/><stop offset=".55" stop-color="#C7A6FF"/><stop offset="1" stop-color="#8A5CFF"/>
    </linearGradient>
`);

/* ------------------------------------------------------------------ */
/* 02 — Nomad Supply (dark commerce)                                   */
/* ------------------------------------------------------------------ */
const nomad = shell("nomad", 1200, 800, `
  <rect width="1200" height="800" fill="#051416"/>
  <circle cx="960" cy="180" r="440" fill="#0E4A4E" opacity=".75"/>
  <circle cx="120" cy="720" r="380" fill="#0A3A40" opacity=".6"/>
  ${browser("nomad", 60, 58, 1080, 684, "#081D1F", `
    <g font-family="${sans}">
      <text x="52" y="55" fill="#F1FFFC" font-size="22" font-weight="700" letter-spacing="5">NOMAD</text>
      <g fill="#9CC6C2" font-size="11.5" font-weight="500">
        <text x="400" y="54">Shop</text><text x="458" y="54">Collections</text><text x="556" y="54">Journal</text><text x="632" y="54">About</text>
      </g>
      <circle cx="900" cy="46" r="17" fill="#fff" fill-opacity=".07"/>
      <path d="M894 40h12l-1.5 13h-9L894 40Z" stroke="#D9FFFA" stroke-width="1.4" stroke-linejoin="round"/>
      <circle cx="899" cy="42" r="3.5" stroke="#D9FFFA" stroke-width="1.4"/>
      <text x="942" y="50" fill="#9CC6C2" font-size="11">Search</text>
      <rect x="52" y="106" width="212" height="30" rx="15" fill="#0FFFCF" fill-opacity=".08"/>
      <circle cx="70" cy="121" r="3.5" fill="#21E6B4"/>
      <text x="82" y="125" fill="#7FFFE2" font-size="9" font-weight="600" letter-spacing="1.4">NEW — ALPINE SERIES 04</text>
      <text x="52" y="206" fill="#F1FFFC" font-size="58" font-weight="700" letter-spacing="-2.4">Gear for the</text>
      <text x="52" y="268" fill="#F1FFFC" font-size="58" font-weight="700" letter-spacing="-2.4">long way <tspan fill="#21E6B4">round.</tspan></text>
      <text x="54" y="314" fill="#8FB8B4" font-size="13.5">Weatherproof essentials, made for the trail</text>
      <text x="54" y="336" fill="#8FB8B4" font-size="13.5">and designed for everywhere in between.</text>
      <rect x="52" y="374" width="152" height="46" rx="23" fill="#21E6B4"/>
      <text x="128" y="402" text-anchor="middle" fill="#05201B" font-size="11.5" font-weight="700">Shop the series</text>
      <rect x="218" y="374" width="152" height="46" rx="23" fill="none" stroke="#fff" stroke-opacity=".2"/>
      <text x="294" y="402" text-anchor="middle" fill="#D9FFFA" font-size="11.5" font-weight="600">Our story</text>
      <g>
        <rect x="52" y="470" width="150" height="160" rx="16" fill="#0B2A2C" stroke="#fff" stroke-opacity=".08"/>
        <rect x="66" y="484" width="122" height="88" rx="10" fill="url(#nomad-p1)"/>
        <path d="M104 514h52c8 0 14 6 14 14v22c0 8-6 14-14 14h-52c-8 0-14-6-14-14v-22c0-8 6-14 14-14Z" fill="#0B1A1B" stroke="#21E6B4" stroke-opacity=".5"/>
        <path d="M112 514v-8c0-10 8-18 18-18s18 8 18 18v8" stroke="#21E6B4" stroke-opacity=".55" stroke-width="3"/>
        <text x="66" y="604" fill="#F1FFFC" font-size="11" font-weight="600">Trail Pack 32L</text>
        <text x="66" y="621" fill="#6F9A96" font-size="9.5">$168.00</text>
        <rect x="218" y="470" width="150" height="160" rx="16" fill="#0B2A2C" stroke="#fff" stroke-opacity=".08"/>
        <rect x="232" y="484" width="122" height="88" rx="10" fill="url(#nomad-p2)"/>
        <path d="M272 500h42v56h-42z" rx="8" fill="#0B1A1B" stroke="#21E6B4" stroke-opacity=".35"/>
        <path d="M272 518h42M272 536h42" stroke="#21E6B4" stroke-opacity=".35"/>
        <text x="232" y="604" fill="#F1FFFC" font-size="11" font-weight="600">Shell Jacket</text>
        <text x="232" y="621" fill="#6F9A96" font-size="9.5">$240.00</text>
        <rect x="384" y="470" width="150" height="160" rx="16" fill="#0B2A2C" stroke="#fff" stroke-opacity=".08"/>
        <rect x="398" y="484" width="122" height="88" rx="10" fill="url(#nomad-p3)"/>
        <circle cx="459" cy="528" r="28" fill="#0B1A1B" stroke="#21E6B4" stroke-opacity=".35"/>
        <path d="M445 528h28M459 514v28" stroke="#21E6B4" stroke-opacity=".45"/>
        <text x="398" y="604" fill="#F1FFFC" font-size="11" font-weight="600">Field Bottle</text>
        <text x="398" y="621" fill="#6F9A96" font-size="9.5">$42.00</text>
      </g>
      <g>
        <rect x="640" y="104" width="368" height="526" rx="22" fill="url(#nomad-hero)" stroke="#fff" stroke-opacity=".12"/>
        <circle cx="824" cy="300" r="132" fill="#0B1A1B" opacity=".55"/>
        <path d="M760 246h122c14 0 26 12 26 26v150c0 16-12 28-28 28H762c-16 0-28-12-28-28V272c0-14 12-26 26-26Z" fill="#102E30" stroke="#21E6B4" stroke-opacity=".45" stroke-width="1.4"/>
        <path d="M762 246v-22c0-36 28-64 64-64s64 28 64 64v22" stroke="#21E6B4" stroke-opacity=".55" stroke-width="6"/>
        <path d="M770 300h122M770 330h80" stroke="#21E6B4" stroke-opacity=".38" stroke-width="3" stroke-linecap="round"/>
        <rect x="668" y="524" width="160" height="66" rx="14" fill="#061D1E" fill-opacity=".86" stroke="#fff" stroke-opacity=".1"/>
        <text x="686" y="552" fill="#F1FFFC" font-size="10" font-weight="700">Alpine Series 04</text>
        <text x="686" y="570" fill="#6F9A96" font-size="9">Limited run · 120 units</text>
        <rect x="832" y="544" width="112" height="26" rx="13" fill="#21E6B4"/>
        <text x="888" y="561" text-anchor="middle" fill="#05201B" font-size="8.5" font-weight="700">EXPLORE</text>
        <circle cx="962" cy="152" r="34" fill="#061D1E" fill-opacity=".8" stroke="#21E6B4" stroke-opacity=".35"/>
        <text x="962" y="149" text-anchor="middle" fill="#21E6B4" font-size="8" letter-spacing="1">FREE</text>
        <text x="962" y="162" text-anchor="middle" fill="#D9FFFA" font-size="8" letter-spacing="1">SHIP</text>
      </g>
      <rect x="640" y="654" width="368" height="1" fill="#fff" fill-opacity=".1"/>
      <text x="640" y="682" fill="#6F9A96" font-size="9" letter-spacing="1.6">CARBON NEUTRAL SHIPPING</text>
      <text x="848" y="682" fill="#6F9A96" font-size="9" letter-spacing="1.6">LIFETIME REPAIRS</text>
    </g>
  `)}
`, `
    <linearGradient id="nomad-hero" x1="640" y1="104" x2="1008" y2="630" gradientUnits="userSpaceOnUse">
      <stop stop-color="#0D3B3E"/><stop offset=".5" stop-color="#09282A"/><stop offset="1" stop-color="#061719"/>
    </linearGradient>
    <linearGradient id="nomad-p1" x1="66" y1="484" x2="188" y2="572" gradientUnits="userSpaceOnUse">
      <stop stop-color="#16494B"/><stop offset="1" stop-color="#0A2426"/>
    </linearGradient>
    <linearGradient id="nomad-p2" x1="232" y1="484" x2="354" y2="572" gradientUnits="userSpaceOnUse">
      <stop stop-color="#1D5A55"/><stop offset="1" stop-color="#0D2A2B"/>
    </linearGradient>
    <linearGradient id="nomad-p3" x1="398" y1="484" x2="520" y2="572" gradientUnits="userSpaceOnUse">
      <stop stop-color="#104349"/><stop offset="1" stop-color="#0A2022"/>
    </linearGradient>
`);

/* ------------------------------------------------------------------ */
/* 03 — Pulseboard (dark SaaS product)                                 */
/* ------------------------------------------------------------------ */
const pulse = shell("pulse", 1200, 800, `
  <rect width="1200" height="800" fill="#080B18"/>
  <circle cx="1030" cy="110" r="400" fill="#2B1B6B" opacity=".7"/>
  <circle cx="130" cy="740" r="360" fill="#073B4C" opacity=".65"/>
  ${browser("pulse", 60, 58, 1080, 684, "#0A0E1C", `
    <g font-family="${sans}">
      <rect x="0" y="0" width="196" height="640" fill="#0C1122"/>
      <path d="M196 0v640" stroke="#fff" stroke-opacity=".07"/>
      <circle cx="40" cy="44" r="12" fill="#7C5CFF"/>
      <path d="M34 44h12M40 38v12" stroke="#fff" stroke-width="1.5"/>
      <text x="60" y="49" fill="#F4F2FF" font-size="14" font-weight="700" letter-spacing="-.2">pulseboard</text>
      <g font-size="10.5" fill="#777E9E">
        <text x="26" y="104" fill="#B9B3FF">Overview</text>
        <text x="26" y="137">Analytics</text>
        <text x="26" y="170">Customers</text>
        <text x="26" y="203">Revenue</text>
        <text x="26" y="236">Experiments</text>
      </g>
      <rect x="16" y="82" width="164" height="34" rx="8" fill="#7C5CFF" fill-opacity=".12"/>
      <g fill="#777E9E" font-size="10.5">
        <text x="26" y="298">WORKSPACE</text>
        <text x="26" y="330">Acme Inc.</text>
        <text x="26" y="363">Design team</text>
      </g>
      <circle cx="30" cy="330" r="3" fill="#45E0B7"/><circle cx="30" cy="363" r="3" fill="#3A405C"/>
      <rect x="16" y="548" width="164" height="60" rx="10" fill="#0FFFCF" fill-opacity=".05" stroke="#fff" stroke-opacity=".07"/>
      <text x="30" y="572" fill="#D9D6FF" font-size="9" font-weight="600">UPGRADE TO PRO</text>
      <text x="30" y="588" fill="#777E9E" font-size="8">Unlock cohort analysis</text>
      <circle cx="48" cy="616" r="12" fill="#1D2340"/>
      <text x="48" y="620" text-anchor="middle" fill="#B9B3FF" font-size="8">AK</text>
      <text x="70" y="612" fill="#D9D6FF" font-size="9">Alex Kim</text>
      <text x="70" y="625" fill="#777E9E" font-size="8">Product lead</text>

      <text x="228" y="44" fill="#F4F2FF" font-size="18" font-weight="700" letter-spacing="-.4">Overview</text>
      <text x="228" y="64" fill="#737B9B" font-size="9.5">Monday, 24 June · Last 30 days</text>
      <rect x="772" y="26" width="78" height="30" rx="8" fill="#111731" stroke="#fff" stroke-opacity=".09"/>
      <text x="811" y="45" text-anchor="middle" fill="#A9B0CE" font-size="9">Last 30d⌄</text>
      <rect x="860" y="26" width="104" height="30" rx="8" fill="#7C5CFF"/>
      <text x="912" y="45" text-anchor="middle" fill="#fff" font-size="9" font-weight="600">+ New report</text>

      <g>
        <rect x="228" y="92" width="222" height="106" rx="12" fill="#0C1224" stroke="#fff" stroke-opacity=".075"/>
        <text x="246" y="118" fill="#737B9B" font-size="8.5" letter-spacing="1.2">MONTHLY RECURRING</text>
        <text x="246" y="152" fill="#F4F2FF" font-size="26" font-weight="700" letter-spacing="-1">$48,290</text>
        <rect x="246" y="166" width="56" height="17" rx="8" fill="#45E0B7" fill-opacity=".12"/>
        <text x="274" y="178" text-anchor="middle" fill="#45E0B7" font-size="8" font-weight="600">+12.4%</text>
        <text x="314" y="178" fill="#5C647F" font-size="8">vs last month</text>
        <path d="M376 112c16 0 16 22 30 14 8-5 10-22 26-18" stroke="#7C5CFF" stroke-width="2" stroke-linecap="round"/>
        <rect x="462" y="92" width="222" height="106" rx="12" fill="#0C1224" stroke="#fff" stroke-opacity=".075"/>
        <text x="480" y="118" fill="#737B9B" font-size="8.5" letter-spacing="1.2">ACTIVE USERS</text>
        <text x="480" y="152" fill="#F4F2FF" font-size="26" font-weight="700" letter-spacing="-1">12,847</text>
        <rect x="480" y="166" width="56" height="17" rx="8" fill="#45E0B7" fill-opacity=".12"/>
        <text x="508" y="178" text-anchor="middle" fill="#45E0B7" font-size="8" font-weight="600">+8.1%</text>
        <text x="548" y="178" fill="#5C647F" font-size="8">vs last month</text>
        <rect x="696" y="92" width="268" height="106" rx="12" fill="#0C1224" stroke="#fff" stroke-opacity=".075"/>
        <text x="714" y="118" fill="#737B9B" font-size="8.5" letter-spacing="1.2">CHURN RATE</text>
        <text x="714" y="152" fill="#F4F2FF" font-size="26" font-weight="700" letter-spacing="-1">1.8%</text>
        <rect x="714" y="166" width="56" height="17" rx="8" fill="#45E0B7" fill-opacity=".12"/>
        <text x="742" y="178" text-anchor="middle" fill="#45E0B7" font-size="8" font-weight="600">-0.3%</text>
        <text x="782" y="178" fill="#5C647F" font-size="8">vs last month</text>
      </g>

      <rect x="228" y="218" width="456" height="230" rx="12" fill="#0C1224" stroke="#fff" stroke-opacity=".075"/>
      <text x="246" y="246" fill="#E4E1FF" font-size="10" font-weight="600">Recurring revenue</text>
      <text x="246" y="262" fill="#5C647F" font-size="8">Daily net revenue across all plans</text>
      <g stroke="#fff" stroke-opacity=".055">
        <path d="M246 300h420M246 336h420M246 372h420M246 408h420"/>
      </g>
      <path d="M246 392c26-8 40-44 64-38s34 30 56 20 28-52 54-46 30 30 54 18 24-42 48-38 28 26 52 18 30-36 52-30 26 18 40 12v78H246Z" fill="url(#pulse-area)"/>
      <path d="M246 392c26-8 40-44 64-38s34 30 56 20 28-52 54-46 30 30 54 18 24-42 48-38 28 26 52 18 30-36 52-30 26 18 40 12" stroke="#8F76FF" stroke-width="2.2" stroke-linecap="round"/>
      <circle cx="618" cy="282" r="4" fill="#0C1224" stroke="#B9B3FF" stroke-width="2"/>
      <rect x="576" y="252" width="84" height="24" rx="6" fill="#171D3C"/>
      <text x="618" y="267" text-anchor="middle" fill="#D9D6FF" font-size="7.5">Jun 18 · $6,420</text>

      <rect x="696" y="218" width="268" height="230" rx="12" fill="#0C1224" stroke="#fff" stroke-opacity=".075"/>
      <text x="714" y="246" fill="#E4E1FF" font-size="10" font-weight="600">Plan mix</text>
      <text x="714" y="262" fill="#5C647F" font-size="8">Revenue by subscription</text>
      <g>
        <circle cx="830" cy="344" r="48" stroke="#151B34" stroke-width="14" fill="none"/>
        <circle cx="830" cy="344" r="48" stroke="#7C5CFF" stroke-width="14" fill="none" stroke-dasharray="152 302" stroke-linecap="round" transform="rotate(-90 830 344)"/>
        <circle cx="830" cy="344" r="48" stroke="#45E0B7" stroke-width="14" fill="none" stroke-dasharray="92 302" stroke-dashoffset="-158" stroke-linecap="round" transform="rotate(-90 830 344)"/>
        <text x="830" y="341" text-anchor="middle" fill="#F4F2FF" font-size="16" font-weight="700">$48k</text>
        <text x="830" y="356" text-anchor="middle" fill="#5C647F" font-size="7.5">TOTAL MRR</text>
        <circle cx="714" cy="412" r="3.5" fill="#7C5CFF"/><text x="724" y="415" fill="#8B91AE" font-size="8">Growth</text>
        <circle cx="778" cy="412" r="3.5" fill="#45E0B7"/><text x="788" y="415" fill="#8B91AE" font-size="8">Scale</text>
        <circle cx="834" cy="412" r="3.5" fill="#2A3152"/><text x="844" y="415" fill="#8B91AE" font-size="8">Starter</text>
      </g>

      <rect x="228" y="468" width="736" height="146" rx="12" fill="#0C1224" stroke="#fff" stroke-opacity=".075"/>
      <text x="246" y="494" fill="#E4E1FF" font-size="10" font-weight="600">Recent activity</text>
      <path d="M246 508h700" stroke="#fff" stroke-opacity=".06"/>
      <g font-size="8.5">
        <circle cx="256" cy="530" r="8" fill="#7C5CFF" fill-opacity=".2"/><text x="274" y="533" fill="#C7C4E4">Acme Inc. upgraded to Scale</text><text x="790" y="533" fill="#5C647F">2m ago</text>
        <circle cx="256" cy="558" r="8" fill="#45E0B7" fill-opacity=".16"/><text x="274" y="561" fill="#C7C4E4">New workspace created — Northstar</text><text x="790" y="561" fill="#5C647F">14m ago</text>
        <circle cx="256" cy="586" r="8" fill="#F5B759" fill-opacity=".16"/><text x="274" y="589" fill="#C7C4E4">Payment failed — retry scheduled</text><text x="790" y="589" fill="#5C647F">1h ago</text>
      </g>
    </g>
  `)}
`, `
    <linearGradient id="pulse-area" x1="246" y1="280" x2="246" y2="440" gradientUnits="userSpaceOnUse">
      <stop stop-color="#7C5CFF" stop-opacity=".38"/><stop offset="1" stop-color="#7C5CFF" stop-opacity="0"/>
    </linearGradient>
`);

/* ------------------------------------------------------------------ */
/* 04 — Verdant (light, organic brand)                                 */
/* ------------------------------------------------------------------ */
const verdant = shell("verdant", 1200, 800, `
  <rect width="1200" height="800" fill="#E7EEE1"/>
  <circle cx="1080" cy="120" r="330" fill="#C7DDB6" opacity=".8"/>
  <circle cx="100" cy="720" r="360" fill="#DDE8CE"/>
  ${browser("verdant", 60, 58, 1080, 684, "#F6F8F1", `
    <g font-family="${sans}">
      <text x="52" y="54" fill="#183D2A" font-family="${serif}" font-size="24" letter-spacing="1">VERDANT</text>
      <g fill="#47634F" font-size="11.5" font-weight="500">
        <text x="430" y="54">Shop</text><text x="490" y="54">Rituals</text><text x="560" y="54">Our roots</text><text x="646" y="54">Journal</text>
      </g>
      <rect x="838" y="28" width="128" height="38" rx="19" fill="#183D2A"/>
      <text x="902" y="51" text-anchor="middle" fill="#F6F8F1" font-size="10.5" font-weight="600">Shop the ritual</text>
      <text x="52" y="124" fill="#6A8266" font-size="9.5" font-weight="600" letter-spacing="2.4">BOTANICAL SKINCARE — SMALL BATCH</text>
      <text x="52" y="214" fill="#183D2A" font-family="${serif}" font-size="76" letter-spacing="-2.6">Rooted in</text>
      <text x="52" y="292" fill="#183D2A" font-family="${serif}" font-size="76" letter-spacing="-2.6">better <tspan fill="#4F7B4C" font-style="italic">care.</tspan></text>
      <text x="54" y="340" fill="#5A7058" font-size="13.5">Plant-powered formulas for skin that thrives.</text>
      <text x="54" y="362" fill="#5A7058" font-size="13.5">Nothing hidden. Nothing wasted.</text>
      <rect x="52" y="398" width="148" height="46" rx="23" fill="#183D2A"/>
      <text x="126" y="426" text-anchor="middle" fill="#F6F8F1" font-size="11" font-weight="600">Explore products</text>
      <text x="226" y="426" fill="#183D2A" font-size="11" font-weight="600">Our ingredients ↗</text>
      <g>
        <circle cx="846" cy="258" r="162" fill="#D7E5C8"/>
        <circle cx="846" cy="258" r="128" fill="none" stroke="#A9C293" stroke-width="1" stroke-dasharray="2 7"/>
        <path d="M792 318c-14-58 22-118 82-140 8 62-22 118-82 140Z" fill="#4F7B4C"/>
        <path d="M806 298c22-42 44-64 62-82" stroke="#D7E5C8" stroke-width="2" stroke-linecap="round"/>
        <path d="M874 194c48 10 78 56 68 108-44 2-74-34-68-108Z" fill="#83A86A"/>
        <path d="M894 222c18 20 30 42 34 62" stroke="#D7E5C8" stroke-width="1.6" stroke-linecap="round"/>
        <rect x="760" y="344" width="180" height="56" rx="28" fill="#F6F8F1"/>
        <circle cx="790" cy="372" r="14" fill="#183D2A"/>
        <path d="M784 372l4 4 8-9" stroke="#F6F8F1" stroke-width="1.5" stroke-linecap="round"/>
        <text x="814" y="368" fill="#183D2A" font-size="9.5" font-weight="600">Certified organic</text>
        <text x="814" y="381" fill="#7A8F75" font-size="8">COSMOS standard</text>
      </g>
      <g>
        <rect x="52" y="480" width="216" height="160" rx="14" fill="#EEF3E8" stroke="#D3E0C7"/>
        <rect x="68" y="496" width="184" height="88" rx="8" fill="url(#verdant-p1)"/>
        <rect x="128" y="512" width="64" height="72" rx="10" fill="#183D2A" fill-opacity=".88"/>
        <rect x="148" y="498" width="24" height="16" rx="3" fill="#83A86A"/>
        <text x="68" y="608" fill="#183D2A" font-size="10" font-weight="600">Forest Dew Serum</text>
        <text x="68" y="625" fill="#7A8F75" font-size="8.5">30ml · $48</text>
        <rect x="284" y="480" width="216" height="160" rx="14" fill="#EEF3E8" stroke="#D3E0C7"/>
        <rect x="300" y="496" width="184" height="88" rx="8" fill="url(#verdant-p2)"/>
        <rect x="366" y="514" width="52" height="70" rx="8" fill="#183D2A" fill-opacity=".88"/>
        <rect x="382" y="500" width="20" height="16" rx="3" fill="#83A86A"/>
        <text x="300" y="608" fill="#183D2A" font-size="10" font-weight="600">Moss Body Oil</text>
        <text x="300" y="625" fill="#7A8F75" font-size="8.5">100ml · $36</text>
      </g>
      <g>
        <rect x="540" y="480" width="456" height="160" rx="14" fill="#183D2A"/>
        <text x="566" y="516" fill="#A9C293" font-size="8.5" letter-spacing="2">FROM OUR JOURNAL</text>
        <text x="566" y="556" fill="#F6F8F1" font-family="${serif}" font-size="30" letter-spacing="-.6">The slow ritual of</text>
        <text x="566" y="588" fill="#F6F8F1" font-family="${serif}" font-size="30" letter-spacing="-.6">morning skincare.</text>
        <circle cx="934" cy="560" r="28" fill="#A9C293"/>
        <path d="M926 560h16M934 552l8 8-8 8" stroke="#183D2A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        <text x="566" y="620" fill="#9DB398" font-size="8.5">4 min read · 12 June</text>
      </g>
    </g>
  `)}
`, `
    <linearGradient id="verdant-p1" x1="68" y1="496" x2="252" y2="584" gradientUnits="userSpaceOnUse">
      <stop stop-color="#DCE8CE"/><stop offset="1" stop-color="#B9D0A3"/>
    </linearGradient>
    <linearGradient id="verdant-p2" x1="300" y1="496" x2="484" y2="584" gradientUnits="userSpaceOnUse">
      <stop stop-color="#E3EBD6"/><stop offset="1" stop-color="#C6DAB2"/>
    </linearGradient>
`);

/* ------------------------------------------------------------------ */
/* 05 — FrameCast (plugin / product interface)                         */
/* ------------------------------------------------------------------ */
const framecast = shell("framecast", 1200, 800, `
  <rect width="1200" height="800" fill="#08060F"/>
  <circle cx="1020" cy="100" r="380" fill="#3A1450" opacity=".8"/>
  <circle cx="120" cy="740" r="340" fill="#082F3A" opacity=".7"/>
  ${browser("framecast", 60, 58, 1080, 684, "#0B0914", `
    <g font-family="${sans}">
      <rect x="0" y="0" width="1080" height="640" fill="#0B0914"/>
      <rect x="0" y="0" width="190" height="640" fill="#0D0B18"/>
      <path d="M190 0v640" stroke="#fff" stroke-opacity=".07"/>
      <circle cx="38" cy="40" r="12" fill="url(#fc-mark)"/>
      <path d="M33 40h10M38 35v10" stroke="#0B0914" stroke-width="1.6"/>
      <text x="58" y="45" fill="#F4F1FF" font-size="14" font-weight="700">FrameCast</text>
      <text x="58" y="58" fill="#6D6786" font-size="7">v2.4.0</text>
      <g font-size="10" fill="#77718F">
        <text x="24" y="104">Dashboard</text><text x="24" y="136">Frames</text><text x="24" y="168">Collections</text><text x="24" y="200">Exports</text>
        <text x="24" y="248">TEAM</text><text x="24" y="280">Anna</text><text x="24" y="312">Marcus</text>
      </g>
      <rect x="14" y="84" width="162" height="34" rx="8" fill="#F472B6" fill-opacity=".1"/>
      <text x="24" y="104" fill="#FFB8D9" font-size="10">Dashboard</text>
      <circle cx="28" cy="280" r="6" fill="#F472B6"/><circle cx="28" cy="312" r="6" fill="#22D3EE"/>
      <rect x="16" y="550" width="158" height="68" rx="10" fill="#141020" stroke="#fff" stroke-opacity=".07"/>
      <text x="28" y="574" fill="#D9D4E8" font-size="8.5" font-weight="600">Storage</text>
      <rect x="28" y="584" width="134" height="5" rx="2.5" fill="#241E33"/>
      <rect x="28" y="584" width="92" height="5" rx="2.5" fill="#F472B6"/>
      <text x="28" y="606" fill="#77718F" font-size="7">3.2 GB of 5 GB used</text>

      <text x="220" y="40" fill="#F4F1FF" font-size="16" font-weight="700">Dashboard</text>
      <text x="220" y="58" fill="#6D6786" font-size="8.5">Wednesday, 24 June</text>
      <rect x="770" y="20" width="34" height="30" rx="8" fill="#141020" stroke="#fff" stroke-opacity=".08"/>
      <circle cx="787" cy="35" r="5" fill="none" stroke="#A29BB8" stroke-width="1.3"/>
      <path d="M791 39l4 4" stroke="#A29BB8" stroke-width="1.3"/>
      <circle cx="834" cy="35" r="13" fill="#F472B6" fill-opacity=".18" stroke="#F472B6" stroke-opacity=".3"/>
      <text x="834" y="39" text-anchor="middle" fill="#FFB8D9" font-size="8">AK</text>
      <rect x="858" y="20" width="122" height="30" rx="15" fill="#F472B6"/>
      <text x="919" y="39" text-anchor="middle" fill="#240B16" font-size="9" font-weight="700">+ New frame</text>

      <g>
        <rect x="220" y="86" width="240" height="96" rx="12" fill="#120E1E" stroke="#fff" stroke-opacity=".07"/>
        <text x="238" y="112" fill="#6D6786" font-size="8" letter-spacing="1">TOTAL FRAMES</text>
        <text x="238" y="148" fill="#F4F1FF" font-size="26" font-weight="700" letter-spacing="-1">1,284</text>
        <rect x="238" y="160" width="48" height="15" rx="7" fill="#22D3EE" fill-opacity=".12"/>
        <text x="262" y="170" text-anchor="middle" fill="#67E8F9" font-size="7">+18.2%</text>
        <rect x="474" y="86" width="240" height="96" rx="12" fill="#120E1E" stroke="#fff" stroke-opacity=".07"/>
        <text x="492" y="112" fill="#6D6786" font-size="8" letter-spacing="1">EXPORTS</text>
        <text x="492" y="148" fill="#F4F1FF" font-size="26" font-weight="700" letter-spacing="-1">8,492</text>
        <rect x="492" y="160" width="48" height="15" rx="7" fill="#F472B6" fill-opacity=".12"/>
        <text x="516" y="170" text-anchor="middle" fill="#FFB8D9" font-size="7">+9.4%</text>
        <rect x="728" y="86" width="252" height="96" rx="12" fill="url(#fc-banner)" stroke="#F472B6" stroke-opacity=".25"/>
        <text x="748" y="112" fill="#FFD4E8" font-size="8" letter-spacing="1">PRO TIP</text>
        <text x="748" y="136" fill="#FFF" font-size="11" font-weight="600">Batch export is now live</text>
        <text x="748" y="152" fill="#E8B8CE" font-size="8">Export every frame in one click →</text>
      </g>

      <text x="220" y="212" fill="#F4F1FF" font-size="11" font-weight="600">Recent frames</text>
      <text x="920" y="212" fill="#F472B6" font-size="8.5" font-weight="600">View all</text>
      <g>
        <rect x="220" y="228" width="240" height="176" rx="12" fill="#120E1E" stroke="#fff" stroke-opacity=".07"/>
        <rect x="234" y="242" width="212" height="116" rx="8" fill="url(#fc-f1)"/>
        <circle cx="340" cy="300" r="34" fill="#0B0914" fill-opacity=".35"/>
        <path d="M328 288h24c4 0 8 4 8 8v14c0 4-4 8-8 8h-24c-4 0-8-4-8-8v-14c0-4 4-8 8-8Z" fill="#F472B6" fill-opacity=".8"/>
        <path d="M332 288v-5c0-5 4-9 8-9s8 4 8 9v5" stroke="#FFD4E8" stroke-width="2"/>
        <text x="234" y="380" fill="#E4DFF0" font-size="9" font-weight="600">Product mockup — 01</text>
        <text x="234" y="394" fill="#6D6786" font-size="7.5">Edited 2h ago</text>
        <rect x="474" y="228" width="240" height="176" rx="12" fill="#120E1E" stroke="#fff" stroke-opacity=".07"/>
        <rect x="488" y="242" width="212" height="116" rx="8" fill="url(#fc-f2)"/>
        <circle cx="594" cy="300" r="34" fill="#0B0914" fill-opacity=".3"/>
        <path d="M580 288h28v24h-28z" stroke="#67E8F9" stroke-opacity=".7" stroke-width="2"/>
        <path d="M588 288v-6h12v6" stroke="#67E8F9" stroke-opacity=".7" stroke-width="2"/>
        <text x="488" y="380" fill="#E4DFF0" font-size="9" font-weight="600">Campaign — Aurora</text>
        <text x="488" y="394" fill="#6D6786" font-size="7.5">Edited yesterday</text>
        <rect x="728" y="228" width="252" height="176" rx="12" fill="#120E1E" stroke="#fff" stroke-opacity=".07"/>
        <rect x="742" y="242" width="224" height="116" rx="8" fill="url(#fc-f3)"/>
        <circle cx="854" cy="300" r="30" fill="#0B0914" fill-opacity=".28"/>
        <path d="M844 288h20v24h-20z" stroke="#F9A8D4" stroke-width="2"/>
        <path d="M840 312l10-10 8 8 6-4 10 12" stroke="#F9A8D4" stroke-width="1.6" stroke-linecap="round"/>
        <text x="742" y="380" fill="#E4DFF0" font-size="9" font-weight="600">Social kit — Spring</text>
        <text x="742" y="394" fill="#6D6786" font-size="7.5">Edited 3d ago</text>
      </g>

      <rect x="220" y="428" width="760" height="188" rx="12" fill="#120E1E" stroke="#fff" stroke-opacity=".07"/>
      <text x="240" y="456" fill="#E4DFF0" font-size="10" font-weight="600">Export activity</text>
      <text x="932" y="456" fill="#6D6786" font-size="8">Last 7 days</text>
      <g stroke="#fff" stroke-opacity=".05">
        <path d="M240 492h720M240 528h720M240 564h720"/>
      </g>
      <g>
        <rect x="250" y="520" width="26" height="64" rx="5" fill="#2A2038"/><rect x="250" y="550" width="26" height="34" rx="5" fill="#F472B6"/>
        <rect x="308" y="504" width="26" height="80" rx="5" fill="#2A2038"/><rect x="308" y="534" width="26" height="50" rx="5" fill="#F472B6"/>
        <rect x="366" y="536" width="26" height="48" rx="5" fill="#2A2038"/><rect x="366" y="558" width="26" height="26" rx="5" fill="#22D3EE"/>
        <rect x="424" y="490" width="26" height="94" rx="5" fill="#2A2038"/><rect x="424" y="512" width="26" height="72" rx="5" fill="#22D3EE"/>
        <rect x="482" y="514" width="26" height="70" rx="5" fill="#2A2038"/><rect x="482" y="544" width="26" height="40" rx="5" fill="#F472B6"/>
        <rect x="540" y="478" width="26" height="106" rx="5" fill="#2A2038"/><rect x="540" y="500" width="26" height="84" rx="5" fill="#F472B6"/>
        <rect x="598" y="496" width="26" height="88" rx="5" fill="#2A2038"/><rect x="598" y="524" width="26" height="60" rx="5" fill="#22D3EE"/>
        <rect x="656" y="532" width="26" height="52" rx="5" fill="#2A2038"/><rect x="656" y="556" width="26" height="28" rx="5" fill="#F472B6"/>
        <rect x="714" y="486" width="26" height="98" rx="5" fill="#2A2038"/><rect x="714" y="514" width="26" height="70" rx="5" fill="#F472B6"/>
        <rect x="772" y="508" width="26" height="76" rx="5" fill="#2A2038"/><rect x="772" y="534" width="26" height="50" rx="5" fill="#22D3EE"/>
      </g>
      <text x="240" y="604" fill="#6D6786" font-size="7">MON</text><text x="356" y="604" fill="#6D6786" font-size="7">WED</text><text x="472" y="604" fill="#6D6786" font-size="7">FRI</text><text x="588" y="604" fill="#6D6786" font-size="7">SUN</text>
      <rect x="790" y="474" width="172" height="124" rx="10" fill="#171222" stroke="#fff" stroke-opacity=".07"/>
      <text x="806" y="498" fill="#6D6786" font-size="7.5" letter-spacing="1">TOP FORMAT</text>
      <text x="806" y="522" fill="#F4F1FF" font-size="12" font-weight="600">Instagram 4:5</text>
      <text x="806" y="540" fill="#6D6786" font-size="7.5">42% of all exports</text>
      <rect x="806" y="554" width="140" height="6" rx="3" fill="#2A2038"/>
      <rect x="806" y="554" width="94" height="6" rx="3" fill="#F472B6"/>
      <text x="806" y="580" fill="#FFB8D9" font-size="7.5">+14% this week</text>
    </g>
  `)}
`, `
    <linearGradient id="fc-mark" x1="26" y1="28" x2="50" y2="52" gradientUnits="userSpaceOnUse"><stop stop-color="#F472B6"/><stop offset="1" stop-color="#22D3EE"/></linearGradient>
    <linearGradient id="fc-banner" x1="728" y1="86" x2="980" y2="182" gradientUnits="userSpaceOnUse"><stop stop-color="#F472B6" stop-opacity=".2"/><stop offset="1" stop-color="#22D3EE" stop-opacity=".06"/></linearGradient>
    <linearGradient id="fc-f1" x1="234" y1="242" x2="446" y2="358" gradientUnits="userSpaceOnUse"><stop stop-color="#3A1D45"/><stop offset="1" stop-color="#191026"/></linearGradient>
    <linearGradient id="fc-f2" x1="488" y1="242" x2="700" y2="358" gradientUnits="userSpaceOnUse"><stop stop-color="#143A46"/><stop offset="1" stop-color="#0C1E26"/></linearGradient>
    <linearGradient id="fc-f3" x1="742" y1="242" x2="966" y2="358" gradientUnits="userSpaceOnUse"><stop stop-color="#3B2331"/><stop offset="1" stop-color="#1C1119"/></linearGradient>
`);

/* ------------------------------------------------------------------ */
/* 06 — Atelier North (warm editorial portfolio)                       */
/* ------------------------------------------------------------------ */
const atelier = shell("atelier", 1200, 800, `
  <rect width="1200" height="800" fill="#EDE6DC"/>
  <circle cx="1060" cy="700" r="360" fill="#E3C6A4" opacity=".5"/>
  <circle cx="90" cy="90" r="300" fill="#F3E8D8" opacity=".8"/>
  ${browser("atelier", 60, 58, 1080, 684, "#F7F2EA", `
    <g font-family="${sans}">
      <text x="52" y="50" fill="#241D17" font-family="${serif}" font-size="19" letter-spacing="3">ATELIER NORTH</text>
      <g fill="#6F6255" font-size="10.5" font-weight="500">
        <text x="790" y="50">Work</text><text x="842" y="50">Studio</text><text x="901" y="50">Journal</text><text x="965" y="50">Contact</text>
      </g>
      <path d="M52 80h976" stroke="#D8CCBD"/>
      <text x="52" y="128" fill="#8C7A66" font-size="9" font-weight="600" letter-spacing="2.4">INTERIOR OBJECTS — COPENHAGEN</text>
      <text x="52" y="228" fill="#241D17" font-family="${serif}" font-size="68" letter-spacing="-2.4">Objects with a</text>
      <text x="52" y="298" fill="#241D17" font-family="${serif}" font-size="68" letter-spacing="-2.4">quiet <tspan fill="#B45309" font-style="italic">presence.</tspan></text>
      <text x="54" y="344" fill="#6F6255" font-size="12.5">A considered collection of handcrafted furniture</text>
      <text x="54" y="364" fill="#6F6255" font-size="12.5">and objects for the modern home.</text>
      <rect x="52" y="398" width="150" height="44" rx="22" fill="#241D17"/>
      <text x="127" y="425" text-anchor="middle" fill="#F7F2EA" font-size="10.5" font-weight="600">View collection</text>
      <text x="222" y="425" fill="#241D17" font-size="10.5" font-weight="600">Our philosophy ↗</text>

      <g>
        <rect x="640" y="96" width="388" height="266" rx="4" fill="url(#atelier-img1)"/>
        <rect x="640" y="96" width="388" height="266" rx="4" fill="none" stroke="#000" stroke-opacity=".07"/>
        <path d="M780 310c0-40 18-94 54-112 36 18 54 72 54 112v22H780v-22Z" fill="#8C6A4A" opacity=".7"/>
        <rect x="826" y="190" width="12" height="86" rx="6" fill="#5E4530" opacity=".8"/>
        <rect x="1000" y="118" width="12" height="12" rx="6" fill="#F7F2EA"/>
        <text x="660" y="134" fill="#F7F2EA" font-size="7.5" letter-spacing="1.8">COLLECTION 04</text>
        <text x="660" y="344" fill="#F7F2EA" font-family="${serif}" font-size="20">The Arc Chair</text>
        <rect x="640" y="380" width="186" height="118" rx="4" fill="url(#atelier-img2)"/>
        <rect x="642" y="382" width="182" height="114" rx="3" fill="none" stroke="#000" stroke-opacity=".06"/>
        <text x="656" y="480" fill="#F7F2EA" font-size="8" letter-spacing="1.2">OAK SERIES · 02</text>
        <rect x="842" y="380" width="186" height="118" rx="4" fill="url(#atelier-img3)"/>
        <circle cx="935" cy="432" r="34" fill="#D8C4AC" opacity=".7"/>
        <path d="M900 474h70" stroke="#6F6255" stroke-width="1" opacity=".4"/>
        <text x="858" y="480" fill="#F7F2EA" font-size="8" letter-spacing="1.2">STONE SERIES · 07</text>
      </g>

      <path d="M52 512h976" stroke="#D8CCBD"/>
      <g>
        <text x="52" y="544" fill="#8C7A66" font-size="8" letter-spacing="1.8">SELECTED WORK</text>
        <rect x="52" y="560" width="200" height="74" rx="4" fill="#E7DACA"/>
        <rect x="66" y="572" width="58" height="50" rx="2" fill="#C9A87F"/>
        <text x="136" y="592" fill="#241D17" font-size="9" font-weight="600">Nordhavn House</text>
        <text x="136" y="608" fill="#8C7A66" font-size="7.5">Residential · 2025</text>
        <rect x="272" y="560" width="200" height="74" rx="4" fill="#E7DACA"/>
        <rect x="286" y="572" width="58" height="50" rx="2" fill="#B9B29E"/>
        <text x="356" y="592" fill="#241D17" font-size="9" font-weight="600">Forma Gallery</text>
        <text x="356" y="608" fill="#8C7A66" font-size="7.5">Cultural · 2025</text>
        <rect x="492" y="560" width="200" height="74" rx="4" fill="#E7DACA"/>
        <rect x="506" y="572" width="58" height="50" rx="2" fill="#D3B99E"/>
        <text x="576" y="592" fill="#241D17" font-size="9" font-weight="600">Kinfill Studio</text>
        <text x="576" y="608" fill="#8C7A66" font-size="7.5">Workspace · 2024</text>
        <rect x="712" y="560" width="316" height="74" rx="4" fill="#241D17"/>
        <text x="732" y="590" fill="#F7F2EA" font-family="${serif}" font-size="18">Have a space in mind?</text>
        <text x="732" y="612" fill="#B7A896" font-size="8">Let's make it feel like home.</text>
        <circle cx="978" cy="596" r="20" fill="#B45309"/>
        <path d="M970 596h16M979 589l7 7-7 7" stroke="#F7F2EA" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
      </g>
      <text x="52" y="680" fill="#8C7A66" font-size="8" letter-spacing="1.2">COPENHAGEN · NEW YORK</text>
      <text x="930" y="680" fill="#8C7A66" font-size="8" letter-spacing="1.2">EST. 2014</text>
    </g>
  `)}
`, `
    <linearGradient id="atelier-img1" x1="640" y1="96" x2="1028" y2="362" gradientUnits="userSpaceOnUse"><stop stop-color="#B79B7C"/><stop offset="1" stop-color="#7C5E42"/></linearGradient>
    <linearGradient id="atelier-img2" x1="640" y1="380" x2="826" y2="498" gradientUnits="userSpaceOnUse"><stop stop-color="#C9B293"/><stop offset="1" stop-color="#9C7C58"/></linearGradient>
    <linearGradient id="atelier-img3" x1="842" y1="380" x2="1028" y2="498" gradientUnits="userSpaceOnUse"><stop stop-color="#B0A48D"/><stop offset="1" stop-color="#817258"/></linearGradient>
`);

/* ------------------------------------------------------------------ */
/* Profile / avatar artwork                                            */
/* ------------------------------------------------------------------ */
const profile = `<svg xmlns="http://www.w3.org/2000/svg" width="800" height="1000" viewBox="0 0 800 1000" fill="none" role="img" aria-labelledby="profileTitle profileDesc">
  <title id="profileTitle">Illustrated portrait placeholder</title>
  <desc id="profileDesc">A stylised illustrated portrait of a developer in a dark violet and cyan palette. Replace with a real photograph before publishing.</desc>
  <defs>
    <linearGradient id="p-bg" x1="0" y1="0" x2="800" y2="1000" gradientUnits="userSpaceOnUse">
      <stop stop-color="#171034"/><stop offset=".55" stop-color="#0D1226"/><stop offset="1" stop-color="#080B16"/>
    </linearGradient>
    <linearGradient id="p-skin" x1="300" y1="280" x2="560" y2="620" gradientUnits="userSpaceOnUse">
      <stop stop-color="#2A2352"/><stop offset=".6" stop-color="#1B2242"/><stop offset="1" stop-color="#121A34"/>
    </linearGradient>
    <linearGradient id="p-hood" x1="220" y1="600" x2="620" y2="1000" gradientUnits="userSpaceOnUse">
      <stop stop-color="#2B1D5C"/><stop offset=".5" stop-color="#191436"/><stop offset="1" stop-color="#0E1024"/>
    </linearGradient>
    <linearGradient id="p-rim" x1="520" y1="260" x2="620" y2="660" gradientUnits="userSpaceOnUse">
      <stop stop-color="#22D3EE" stop-opacity=".9"/><stop offset="1" stop-color="#8B5CF6" stop-opacity="0"/>
    </linearGradient>
    <linearGradient id="p-accent" x1="140" y1="160" x2="660" y2="860" gradientUnits="userSpaceOnUse">
      <stop stop-color="#8B5CF6"/><stop offset="1" stop-color="#06B6D4"/>
    </linearGradient>
    <filter id="p-blur" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="70"/></filter>
    <filter id="p-grain" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".85" numOctaves="2"/><feColorMatrix type="saturate" values="0"/></filter>
    <filter id="p-shadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="24" stdDeviation="30" flood-color="#000" flood-opacity=".45"/></filter>
  </defs>
  <rect width="800" height="1000" rx="36" fill="url(#p-bg)"/>
  <circle cx="620" cy="230" r="230" fill="#8B5CF6" opacity=".38" filter="url(#p-blur)"/>
  <circle cx="160" cy="820" r="240" fill="#06B6D4" opacity=".22" filter="url(#p-blur)"/>
  <g opacity=".18" stroke="#A78BFA" stroke-width="1">
    <path d="M0 200h800M0 400h800M0 600h800M0 800h800M200 0v1000M400 0v1000M600 0v1000"/>
  </g>
  <circle cx="400" cy="460" r="252" stroke="#8B5CF6" stroke-opacity=".28" stroke-dasharray="2 12"/>
  <circle cx="400" cy="460" r="300" stroke="#06B6D4" stroke-opacity=".14"/>
  <g opacity=".5" fill="#A78BFA">
    <circle cx="155" cy="330" r="3"/><circle cx="672" cy="520" r="2.5"/><circle cx="210" cy="640" r="2"/><circle cx="590" cy="150" r="2"/>
  </g>
  <text x="400" y="480" text-anchor="middle" fill="#fff" fill-opacity=".035" font-family="'Segoe UI', Arial, sans-serif" font-size="360" font-weight="700" letter-spacing="-20">IM</text>

  <g filter="url(#p-shadow)">
    <path d="M160 1000c0-176 86-286 240-286s240 110 240 286H160Z" fill="url(#p-hood)"/>
    <path d="M240 968c4-130 54-212 160-212s156 82 160 212" stroke="#8B5CF6" stroke-opacity=".25" stroke-width="2"/>
    <path d="M308 726c8 48 36 78 92 78s84-30 92-78" fill="#151B35"/>
    <path d="M304 646c-4 72 26 118 96 118s100-46 96-118v-112H304v112Z" fill="url(#p-skin)"/>
    <path d="M300 546c0-112 44-186 100-186s100 74 100 186v92c0 82-44 134-100 134s-100-52-100-134v-92Z" fill="url(#p-skin)"/>
    <path d="M292 532c10-104 56-174 110-174 60 0 98 68 106 174-20-44-50-70-94-78-42-8-84 12-122 78Z" fill="#0E1329"/>
    <path d="M302 452c18-60 58-92 106-92 50 0 88 30 104 88-26-28-60-42-104-42-42 0-78 14-106 46Z" fill="#1A1238"/>
    <path d="M478 420c18 34 24 72 20 116" stroke="url(#p-rim)" stroke-width="3" stroke-linecap="round"/>
    <path d="M294 444c-6 76 2 136 22 184" stroke="#8B5CF6" stroke-opacity=".25" stroke-width="2" stroke-linecap="round"/>
    <g>
      <rect x="326" y="548" width="66" height="48" rx="16" stroke="#22D3EE" stroke-opacity=".85" stroke-width="2.5" fill="#06B6D4" fill-opacity=".06"/>
      <rect x="408" y="548" width="66" height="48" rx="16" stroke="#22D3EE" stroke-opacity=".85" stroke-width="2.5" fill="#06B6D4" fill-opacity=".06"/>
      <path d="M392 566c5-4 11-4 16 0M326 564c-6-2-12 1-14 6" stroke="#22D3EE" stroke-opacity=".7" stroke-width="2" stroke-linecap="round"/>
      <rect x="342" y="564" width="36" height="18" rx="7" fill="#22D3EE" fill-opacity=".1"/>
      <rect x="422" y="564" width="36" height="18" rx="7" fill="#22D3EE" fill-opacity=".1"/>
      <path d="M386 560l6-10" stroke="#22D3EE" stroke-opacity=".5" stroke-width="2"/>
      <circle cx="346" cy="570" r="2.5" fill="#67E8F9"/><circle cx="426" cy="570" r="2.5" fill="#67E8F9"/>
    </g>
    <path d="M396 610c2 8 6 12 12 12s10-4 12-12" stroke="#22D3EE" stroke-opacity=".3" stroke-width="2" stroke-linecap="round"/>
    <path d="M368 664c20 10 42 10 62 0" stroke="#A78BFA" stroke-opacity=".45" stroke-width="3" stroke-linecap="round"/>
    <path d="M456 692c28 14 44 40 48 78" stroke="#22D3EE" stroke-opacity=".35" stroke-width="2"/>
    <path d="M500 730c-10 30-6 70 14 100" stroke="#8B5CF6" stroke-opacity=".3" stroke-width="2"/>
  </g>

  <g>
    <rect x="44" y="44" width="200" height="52" rx="26" fill="#0B1020" fill-opacity=".65" stroke="#fff" stroke-opacity=".12"/>
    <circle cx="72" cy="70" r="5" fill="#34D399"/>
    <circle cx="72" cy="70" r="10" stroke="#34D399" stroke-opacity=".3"/>
    <text x="88" y="66" fill="#fff" font-family="'Segoe UI', Arial, sans-serif" font-size="11" font-weight="600">Available for work</text>
    <text x="88" y="82" fill="#A1A1AA" font-family="'Segoe UI', Arial, sans-serif" font-size="9">Remote · worldwide</text>
  </g>
  <g>
    <rect x="44" y="828" width="712" height="128" rx="24" fill="#0B1020" fill-opacity=".72" stroke="#fff" stroke-opacity=".12"/>
    <text x="76" y="876" fill="#fff" font-family="'Segoe UI', Arial, sans-serif" font-size="26" font-weight="600" letter-spacing="-.4">Imran</text>
    <text x="76" y="900" fill="#A1A1AA" font-family="'Segoe UI', Arial, sans-serif" font-size="11" letter-spacing="1.4">WEB DESIGNER &amp; WORDPRESS DEVELOPER</text>
    <rect x="76" y="916" width="64" height="4" rx="2" fill="url(#p-accent)"/>
    <g transform="translate(620 872)">
      <circle cx="0" cy="0" r="18" fill="#8B5CF6" fill-opacity=".15" stroke="#8B5CF6" stroke-opacity=".4"/>
      <path d="M-5 1l4 4 7-9" stroke="#A78BFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </g>
    <text x="652" y="876" fill="#fff" font-family="'Segoe UI', Arial, sans-serif" font-size="10" font-weight="600">5+ years</text>
    <text x="652" y="890" fill="#A1A1AA" font-family="'Segoe UI', Arial, sans-serif" font-size="8">crafting the web</text>
  </g>
  <rect x="44" y="944" width="712" height="1" fill="#fff" fill-opacity=".08"/>
  <text x="44" y="972" fill="#A1A1AA" fill-opacity=".7" font-family="'Segoe UI', Arial, sans-serif" font-size="9" letter-spacing="1.6">PORTFOLIO / 2026</text>
  <text x="756" y="972" text-anchor="end" fill="#A1A1AA" fill-opacity=".7" font-family="'Segoe UI', Arial, sans-serif" font-size="9" letter-spacing="1.6">DESIGN × CODE</text>
  <rect width="800" height="1000" rx="36" filter="url(#p-grain)" opacity=".045" style="mix-blend-mode:overlay"/>
</svg>`;

/* ------------------------------------------------------------------ */
/* Favicon                                                             */
/* ------------------------------------------------------------------ */
const favicon = `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">
  <defs><linearGradient id="f" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse"><stop stop-color="#8B5CF6"/><stop offset="1" stop-color="#06B6D4"/></linearGradient></defs>
  <rect width="64" height="64" rx="16" fill="#080B16"/>
  <rect x="4" y="4" width="56" height="56" rx="13" fill="url(#f)" fill-opacity=".14" stroke="url(#f)"/>
  <path d="M32 13l4.8 14.2L51 32l-14.2 4.8L32 51l-4.8-14.2L13 32l14.2-4.8L32 13Z" fill="url(#f)"/>
  <circle cx="32" cy="32" r="4.2" fill="#080B16"/>
</svg>`;

const covers = {
  "project-lumen.svg": lumen,
  "project-nomad.svg": nomad,
  "project-pulseboard.svg": pulse,
  "project-verdant.svg": verdant,
  "project-framecast.svg": framecast,
  "project-atelier.svg": atelier,
};

for (const [name, svg] of Object.entries(covers)) {
  writeFileSync(join(out, "projects", name), svg.trim() + "\n");
}
writeFileSync(join(out, "profile.svg"), profile.trim() + "\n");
writeFileSync(join(out, "favicon.svg"), favicon.trim() + "\n");
console.log("Generated", Object.keys(covers).length + 2, "SVG assets.");
