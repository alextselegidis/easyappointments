<?php
/**
 * @var string $heading
 * @var string $message
 */
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 — BUUMER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,500;0,700;1,400&family=Inter:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:#0A0E14;--bg2:#0F1419;--bg3:#1A1F26;--text:#E6E9F0;--muted:#68747D;--border:#2C313A;
            --blue:#9CDCFE;--purple:#C586C0;--orange:#CE9178;--green:#B5CEA8;--green2:#6A9955;--yellow:#FFE484;
            --red:#F48771;--fn:#DCDCAA;--gold:#D7BA7D;--mono:'JetBrains Mono', monospace;--sans:'Inter', sans-serif;
        }
        html, body { height: 100%; background: var(--bg); color: var(--text); font-family: var(--sans); overflow: hidden; }
        body::before {
            content: ''; position: fixed; inset: 0;
            background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 40px 40px; opacity: 0.25; pointer-events: none;
        }
        .blob { position: fixed; border-radius: 50%; filter: blur(120px); opacity: 0.12; pointer-events: none; }
        .blob-1 { width: 500px; height: 500px; background: var(--blue); top: -150px; left: -100px; animation: float 8s ease-in-out infinite; }
        .blob-2 { width: 400px; height: 400px; background: var(--purple); bottom: -100px; right: -80px; animation: float 10s ease-in-out infinite reverse; }
        @keyframes float { 0%,100% { transform: translateY(0px);} 50% { transform: translateY(30px);} }
        .page {
            position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            min-height: 100vh; padding: 2rem;
        }
        .header {
            position: fixed; top: 0; left: 0; right: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem; border-bottom: 1px solid var(--border); background: rgba(10, 14, 20, 0.85); backdrop-filter: blur(12px);
        }
        .logo { font-family: var(--mono); font-weight: 700; font-size: 1.1rem; color: var(--fn); letter-spacing: 0.1em; }
        .logo span { color: var(--blue); }
        .header-comment { font-family: var(--mono); font-size: 0.78rem; color: var(--muted); font-style: italic; }
        .editor {
            width: 100%; max-width: 700px; background: var(--bg2); border: 1px solid var(--border); border-radius: 10px; overflow: hidden;
            box-shadow: 0 0 0 1px var(--border), 0 40px 80px rgba(0,0,0,0.6); animation: slideUp 0.6s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px);} to { opacity: 1; transform: translateY(0);} }
        .titlebar { display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: var(--bg3); border-bottom: 1px solid var(--border); }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot-r { background: #F48771; } .dot-y { background: #FFE484; opacity: 0.6; } .dot-g { background: #6A9955; opacity: 0.6; }
        .filename { font-family: var(--mono); font-size: 0.75rem; color: var(--muted); margin-left: 0.5rem; }
        .filename .active { color: var(--text); }
        .code-body { display: flex; font-family: var(--mono); font-size: 0.88rem; line-height: 1.8; padding: 1.5rem 0; }
        .line-numbers {
            padding: 0 1rem 0 1.5rem; text-align: right; color: var(--muted); user-select: none; font-size: 0.8rem;
            min-width: 3.5rem; border-right: 1px solid var(--border);
        }
        .line-numbers span { display: block; }
        .code-lines { padding: 0 2rem; flex: 1; } .code-lines .line { display: block; }
        .kw { color: var(--purple); } .fn { color: var(--fn); } .str { color: var(--orange); } .num { color: var(--green); }
        .var { color: var(--blue); } .cmt { color: var(--muted); font-style: italic; } .err { color: var(--red); }
        .cursor { display: inline-block; width: 9px; height: 1em; background: var(--blue); vertical-align: text-bottom; animation: blink 1s step-end infinite; }
        @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0; } }
        .error-display { text-align: center; padding: 1.5rem 2rem 0; animation: slideUp 0.6s 0.15s cubic-bezier(0.16,1,0.3,1) both; }
        .error-code {
            font-family: var(--mono); font-size: clamp(5rem, 20vw, 9rem); font-weight: 700; color: var(--red); line-height: 1;
            letter-spacing: -0.04em; text-shadow: 0 0 60px rgba(244,135,113,0.3); position: relative;
        }
        .error-code .zero { color: var(--fn); }
        .error-label { font-family: var(--mono); font-size: 0.85rem; color: var(--muted); margin-top: 0.25rem; font-style: italic; }
        .actions {
            display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; padding: 2rem;
            animation: slideUp 0.6s 0.35s cubic-bezier(0.16,1,0.3,1) both;
        }
        .btn {
            font-family: var(--mono); font-size: 0.85rem; padding: 0.65rem 1.5rem; border-radius: 20px; text-decoration: none;
            transition: all 0.2s; cursor: pointer; border: 1px solid; display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary { border-color: var(--blue); color: var(--blue); background: transparent; }
        .btn-primary:hover { background: rgba(156, 220, 254, 0.1); box-shadow: 0 0 12px rgba(156, 220, 254, 0.15); }
        .btn-secondary { border-color: var(--border); color: var(--text); background: transparent; }
        .btn-secondary:hover { border-color: var(--muted); background: rgba(255,255,255,0.04); }
        .footer-note {
            margin-top: 2.5rem; font-family: var(--mono); font-size: 0.75rem; color: var(--muted); font-style: italic;
            text-align: center; animation: slideUp 0.6s 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        .error-code::before, .error-code::after { content: attr(data-text); position: absolute; inset: 0; opacity: 0; }
        .error-code::before { color: var(--blue); clip-path: inset(20% 0 60% 0); }
        .error-code::after  { color: var(--purple); clip-path: inset(60% 0 10% 0); }
        .error-code:hover::before { opacity: 0.6; animation: glitch-1 0.3s steps(4) forwards; }
        .error-code:hover::after  { opacity: 0.6; animation: glitch-2 0.3s steps(4) forwards; }
        @keyframes glitch-1 { 0% {transform: translate(-3px, 2px);} 25% {transform: translate(2px, -1px);} 75% {transform: translate(-1px, 3px);} 100% {transform: translate(0, 0); opacity: 0;} }
        @keyframes glitch-2 { 0% {transform: translate(3px, -2px);} 50% {transform: translate(-2px, 1px);} 100% {transform: translate(0, 0); opacity: 0;} }
        @media (max-width: 600px) { .header-comment { display: none; } .code-body { font-size: 0.78rem; } .code-lines { padding: 0 1rem; } }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <header class="header">
        <div class="logo">BUUM<span>ER</span></div>
        <div class="header-comment">// pagina non trovata</div>
    </header>

    <main class="page">
        <div class="error-display">
            <div class="error-code" data-text="4&zwj;0&zwj;4">
                <span style="color:var(--red)">4</span><span class="zero">0</span><span style="color:var(--red)">4</span>
            </div>
            <div class="error-label">// Error: route not found</div>
        </div>

        <div class="editor">
            <div class="titlebar">
                <div class="dot dot-r"></div>
                <div class="dot dot-y"></div>
                <div class="dot dot-g"></div>
                <div class="filename">
                    <span>errors.js</span>
                    &nbsp;›&nbsp;
                    <span class="active">handleRoute()</span>
                </div>
            </div>

            <div class="code-body">
                <div class="line-numbers">
                    <span>01</span><span>02</span><span>03</span><span>04</span><span>05</span>
                    <span>06</span><span>07</span><span>08</span><span>09</span><span>10</span>
                    <span>11</span><span>12</span>
                </div>
                <div class="code-lines">
                    <span class="line"><span class="cmt">// BUUMER — sistema di prenotazione</span></span>
                    <span class="line">&nbsp;</span>
                    <span class="line"><span class="kw">async function</span> <span class="fn">handleRoute</span>(<span class="var">request</span>) {</span>
                    <span class="line">&nbsp;&nbsp;<span class="kw">const</span> <span class="var">path</span> = <span class="var">request</span>.<span class="fn">url</span>;</span>
                    <span class="line">&nbsp;&nbsp;<span class="kw">const</span> <span class="var">route</span> = <span class="fn">router</span>.<span class="fn">match</span>(<span class="var">path</span>);</span>
                    <span class="line">&nbsp;</span>
                    <span class="line">&nbsp;&nbsp;<span class="kw">if</span> (!<span class="var">route</span>) {</span>
                    <span class="line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="fn">throw</span> <span class="kw">new</span> <span class="err">RouteNotFoundError</span>({</span>
                    <span class="line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="var">status</span>: <span class="num">404</span>,</span>
                    <span class="line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="var">message</span>: <span class="str">"La pagina non esiste"</span></span>
                    <span class="line">&nbsp;&nbsp;&nbsp;&nbsp;});</span>
                    <span class="line">&nbsp;&nbsp;} <span class="cmt">// ← sei qui</span> <span class="cursor"></span></span>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="/" class="btn btn-primary">
                <span>←</span> Torna alla Home
            </a>
            <a href="/prenota/" class="btn btn-secondary">
                <span>📅</span> Prenota ora
            </a>
        </div>

        <div class="footer-note">
            // Se il problema persiste, contatta <span style="color:var(--blue)">support@buumer.it</span>
        </div>
    </main>
</body>
</html>
