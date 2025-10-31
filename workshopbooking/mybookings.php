<?php
// 'Meine Buchungen' – Outlook-/Microsoft-ähnliches, professionelles Design (nur Optik)
// + Wochenenden + PRO-Tooltips (korrekte Position) + WEISS als Akzent im Tooltip
// + in Dark-Mode: Chips VM/NM/other in Weiß (Kontrast auf schwarzem Hintergrund).
// Logik bleibt unverändert.

require(__DIR__ . '/../../config.php');

$cmid  = required_param('id', PARAM_INT);
$view  = optional_param('view', 'month', PARAM_ALPHA);   // month|week|day
$qdate = optional_param('date', '', PARAM_RAW_TRIMMED);  // 'YYYY-MM-DD'

list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'workshopbooking');
$context = context_module::instance($cm->id);
require_login($course, false, $cm);
require_capability('mod/workshopbooking:view', $context);

global $DB, $OUTPUT, $USER, $PAGE;

$PAGE->set_url(new moodle_url('/mod/workshopbooking/mybookings.php', ['id'=>$cm->id]));
$PAGE->set_title(get_string('mybookings', 'mod_workshopbooking'));
$PAGE->set_heading(format_string($course->fullname, true));

function wsb_str($key, $fallback) {
    $s = get_string($key, 'mod_workshopbooking');
    return ($s === "[[{$key}]]") ? $fallback : $s;
}

$instance = $DB->get_record('workshopbooking', ['id'=>$cm->instance], '*', MUST_EXIST);

// Bestätigte Buchungen (12 Monate rückwärts + Zukunft).
$since = time() - (DAYSECS * 365);
$sql = "SELECT s.id, s.name, s.timestart, s.slot
          FROM {workshopbooking_booking} b
          JOIN {workshopbooking_session} s ON s.id = b.sessionid
         WHERE s.workshopbookingid = :iid
           AND b.userid = :uid
           AND b.status = 1
           AND s.timestart >= :since
      ORDER BY s.timestart ASC";
$rows = $DB->get_records_sql($sql, ['iid'=>$instance->id, 'uid'=>$USER->id, 'since'=>$since]);

$events = [];
$firstfuture = 0; $now = time();
foreach ($rows as $s) {
    $duration = 3600; // 60 Minuten Anzeige
    $name     = (string)($s->name ?? 'Workshop');
    $slot     = trim(strtoupper((string)($s->slot ?? '')));
    $title    = preg_replace('/\s*-\s*(VM|NM)$/i', '', $name) . ($slot ? ' (' . $slot . ')' : '');
    $desc     = format_string($title) . ' — ' . userdate($s->timestart, get_string('strftimedatetime', 'langconfig'));

    $events[] = [
        'id'    => (int)$s->id,
        'name'  => $name,
        'title' => $title,
        'slot'  => $slot,
        'desc'  => $desc,
        'start' => (int)$s->timestart,
        'end'   => (int)$s->timestart + $duration,
    ];
    if (!$firstfuture && $s->timestart >= $now) { $firstfuture = (int)$s->timestart; }
}

$initialdate = $qdate ?: ($firstfuture ? userdate($firstfuture, '%Y-%m-%d') : userdate(time(), '%Y-%m-%d'));
$icsurl = (new moodle_url('/mod/workshopbooking/exportuserics.php', ['id'=>$cm->id]))->out(false);

echo $OUTPUT->header();
echo $OUTPUT->heading(wsb_str('mybookings', 'Meine Buchungen'));

// Toolbar
echo html_writer::start_div('', ['id'=>'sb-toolbar', 'style'=>'display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:space-between;margin:8px 0 16px;']);
    echo html_writer::start_div('', ['style'=>'display:flex;gap:8px;flex-wrap:wrap;align-items:center;']);
        echo html_writer::tag('button', get_string('today'), ['id'=>'sb-today','class'=>'btn btn-secondary']);
        echo html_writer::select(['month'=>wsb_str('view_month','Monat'),'week'=>wsb_str('view_week','Woche'),'day'=>wsb_str('view_day','Tag')],'sb-view', s($view), false, ['id'=>'sb-view','class'=>'custom-select']);
        echo html_writer::start_span('', ['style'=>'display:flex;gap:6px;align-items:center;']);
            echo html_writer::label(wsb_str('jump_to','Springen zu'), 'sb-date', false, ['class'=>'sr-only']);
            echo html_writer::empty_tag('input', ['id'=>'sb-date','type'=>'date','class'=>'form-control']);
            echo html_writer::empty_tag('input', ['id'=>'sb-month','type'=>'month','class'=>'form-control']);
        echo html_writer::end_span();
        echo html_writer::select([''=>wsb_str('slot_all','Alle Slots'),'VM'=>wsb_str('slot_vm','VM'),'NM'=>wsb_str('slot_nm','NM')],'sb-slot','',false,['id'=>'sb-slot','class'=>'custom-select']);
    echo html_writer::end_div();
    echo html_writer::link($icsurl, wsb_str('downloadics','.ics herunterladen'), ['class'=>'btn btn-secondary']);
echo html_writer::end_div();

// Container + Tooltip-Root
echo html_writer::div('', 'sbcal', ['id'=>'sbcal']);
echo html_writer::div('', '', ['id'=>'sb-tooltip-root']); // globaler Tooltip-Container

// ===== CSS =====
$css = "
:root {
  --sb-border: #e1dfdd;
  --sb-surface: #ffffff;
  --sb-surface-alt: #faf9f8;
  --sb-text: #201f1e;
  --sb-muted: #605e5c;
  --sb-accent: #0f6cbd;
  --sb-radius: 10px;
  --sb-radius-sm: 8px;
  --sb-shadow: 0 1px 2px rgba(0,0,0,.04), 0 6px 18px rgba(0,0,0,.06);
  --sb-weekend: #f6f6f6;
  --sb-weekend-head: #f0f0f0;
  --sb-today-bg: #ecf6ff;
  --sb-today-bd: #0f6cbd;
}

.sbcal {
  background: var(--sb-surface);
  border: 1px solid var(--sb-border);
  border-radius: var(--sb-radius);
  overflow: hidden;
  box-shadow: var(--sb-shadow);
  color: var(--sb-text);
  font-size: 13px;
  line-height: 1.35;
}

.sbcal table { width:100%; border-collapse: separate; border-spacing:0; table-layout: fixed; }
.sbcal thead th {
  position: sticky; top: 0; z-index: 1;
  background: var(--sb-surface-alt);
  font-weight: 600; letter-spacing: .02em;
  text-align: left; color: var(--sb-muted);
  padding: 8px 10px; border-bottom: 1px solid var(--sb-border);
  font-size: 12.5px;
}
.sbcal tbody td {
  border-right: 1px solid var(--sb-border);
  border-bottom: 1px solid var(--sb-border);
  padding: 6px 7px 8px 7px;
  min-height: 78px; height: 78px;
  vertical-align: top; background: var(--sb-surface);
}
.sbcal tbody tr > td:last-child { border-right: 0; }

.sbcal .sb-othermonth { background: var(--sb-surface-alt); color: var(--sb-muted); }

/* Wochenenden (Monat & Woche) – dezent */
.sbcal.sb-month thead tr > th:nth-child(6),
.sbcal.sb-month thead tr > th:nth-child(7),
.sbcal.sb-week  thead tr > th:nth-child(6),
.sbcal.sb-week  thead tr > th:nth-child(7) { background: var(--sb-weekend-head); color: var(--sb-muted); }
.sbcal.sb-month tbody tr > td:nth-child(6),
.sbcal.sb-month tbody tr > td:nth-child(7),
.sbcal.sb-week  tbody tr > td:nth-child(6),
.sbcal.sb-week  tbody tr > td:nth-child(7) { background: var(--sb-weekend); }

.sbcal td.is-today { box-shadow: inset 0 0 0 2px var(--sb-today-bd); background: var(--sb-today-bg); }

/* Datum */
.sbcal .sb-daynum { float: right; font-weight: 600; color: #323130; font-size: 12px; padding-top: 2px; opacity: .9; }

/* Event-Chips – Microsoft-like */
.sbcal .sb-chip {
  position: relative;
  display: block;
  margin: 4px 0;
  padding: 4px 8px 4px 10px;
  border-radius: 6px;
  border: 1px solid transparent;
  background: #fbfbfb;
  font-size: .93em;
  line-height: 1.25;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  font-variant-numeric: tabular-nums;
  transition: background-color .12s ease, box-shadow .12s ease, transform .12s ease;
  cursor: default;
}
.sbcal .sb-chip::before {
  content: ''; position: absolute; left: 0; top: 3px; bottom: 3px;
  width: 3px; border-radius: 2px; background: currentColor; opacity: .9;
}
.sbcal .sb-chip:hover { background: #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,.08); transform: translateY(-1px); }

/* Slot-Farben (Light Mode beibehalten) */
.sbcal .sb-chip--vm { color:#0f6cbd !important; background:#f0f6fd !important; border-color:#d6e6fb !important; }
.sbcal .sb-chip--nm { color:#107c10 !important; background:#edf7ee !important; border-color:#cfe7d2 !important; }
.sbcal .sb-chip--other { color:#8a6d3b !important; background:#fff6e5 !important; border-color:#ffe2b8 !important; }

/* Woche/Tag Kopfzeile */
.sbcal.sb-week thead th, .sbcal.sb-day thead th { background: var(--sb-surface-alt); }

/* --- PRO Tooltip --- */
#sb-tooltip-root { position: fixed; inset: 0; pointer-events: none; z-index: 9999; }
.sb-tooltip {
  position: fixed; left: 0; top: 0;
  background: #252423; color: #fff;  /* Text immer weiß */
  padding: 8px 10px;
  font-size: 12px; line-height: 1.25;
  border-radius: 6px;
  box-shadow: 0 10px 24px rgba(0,0,0,.22);
  min-width: 160px; max-width: 320px;
  text-align: left; white-space: normal;
  opacity: 0; transition: opacity .12s ease;
  border-top: 2px solid #fff;        /* Akzent immer weiß */
}
.sb-tooltip.is-visible { opacity: 1; }
.sb-tooltip::after {
  content: ''; position: absolute; width: 0; height: 0; border: 6px solid transparent;
}
.sb-tooltip.top::after    { top: 100%;  left: var(--arrow-x, 50%); transform: translateX(-50%); border-bottom-color: #252423; }
.sb-tooltip.bottom        { border-top: none; border-bottom: 2px solid #fff; }
.sb-tooltip.bottom::after { bottom: 100%;left: var(--arrow-x, 50%); transform: translateX(-50%); border-top-color: #252423; }

/* Accessibility */
.sbcal .sb-chip:focus-visible { outline: 2px solid var(--sb-accent); outline-offset: 2px; }

/* Responsive & Motion */
@media (prefers-reduced-motion: reduce) { .sbcal .sb-chip, .sb-tooltip { transition: none !important; } }
@media (max-width: 1024px) { .sbcal { font-size: 12.6px; } .sbcal tbody td { height: 72px; min-height: 72px; padding: 6px; } }
@media (max-width: 680px) { .sbcal { border-radius: 8px; } .sbcal thead th { padding: 7px 8px; font-size: 12px; } .sbcal tbody td { height: 64px; min-height: 64px; } .sbcal .sb-chip { font-size: .92em; } }

/* Dark Mode – Chips in Weiß (besserer Kontrast auf schwarz) */
@media (prefers-color-scheme: dark) {
  :root {
    --sb-border: #2a2a2a; --sb-surface: #121212; --sb-surface-alt: #161616;
    --sb-text: #e5e5e5; --sb-muted: #b3b3b3;
    --sb-weekend: #141414; --sb-weekend-head: #181818;
    --sb-today-bg: #0b2540; --sb-today-bd: #478fcc;
  }
  .sbcal { box-shadow: 0 1px 2px rgba(0,0,0,.35), 0 8px 22px rgba(0,0,0,.3); }
  .sbcal .sb-chip { background: #1a1a1a; border-color: #252525; color: #e0e0e0; }
  .sbcal .sb-chip--vm,
  .sbcal .sb-chip--nm,
  .sbcal .sb-chip--other { color:#fff !important; background: rgba(255,255,255,.08) !important; border-color: rgba(255,255,255,.18) !important; }
  .sbcal .sb-chip::before { background: #fff; } /* linker Balken ebenfalls weiß */
}

/* Print */
@media print {
  .sbcal { box-shadow: none; border: 1px solid #000; }
  .sbcal thead th { position: static; background: #fff; border-bottom: 1px solid #000; color: #000; }
  .sbcal tbody td { border-color: #000; }
}
";
echo html_writer::tag('style', $css);

// ===== JS – Render + Steuerung (unverändert) =====
$eventsjson = json_encode($events);
$initview   = json_encode($view);
$initdate   = json_encode($initialdate);

$js = <<<JS
(function(){
  "use strict";
  var events = $eventsjson;
  var state = { view: $initview, date: parseIsoDate($initdate) || new Date(), slot: '' };

  function parseIsoDate(str){ if(!str||typeof str!=='string')return null; var m=str.match(/^(\\d{4})-(\\d{2})-(\\d{2})$/); if(!m)return null; var y=+m[1], mo=+m[2]-1, d=+m[3]; var dt=new Date(y,mo,d,0,0,0,0); return isNaN(dt.getTime())?null:dt; }
  function startOfWeek(dt){ var d=new Date(dt); var day=(d.getDay()||7); if(day!==1)d.setDate(d.getDate()-day+1); d.setHours(0,0,0,0); return d; }
  function startOfMonth(dt){ var d=new Date(dt.getFullYear(), dt.getMonth(), 1); d.setHours(0,0,0,0); return d; }
  function pad2(n){ return String(n).padStart(2,'0'); }
  function timeLabel(sec){ var d=new Date(sec*1000); return pad2(d.getHours())+':'+pad2(d.getMinutes()); }
  function formatISO(dt){ var y=dt.getFullYear(), m=('0'+(dt.getMonth()+1)).slice(-2), d=('0'+dt.getDate()).slice(-2); return y+'-'+m+'-'+d; }
  function isWeekend(d){ var wd=d.getDay(); return wd===6 || wd===0; }
  function isSameDate(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
  function clsFor(slot){ var k=(slot||'').toString().trim().toUpperCase(); if(k==='VM'||k==='VORMITTAG')return 'sb-chip--vm'; if(k==='NM'||k==='NACHMITTAG')return 'sb-chip--nm'; return 'sb-chip--other'; }
  function filterEvents(dayStart, dayEnd){
    var rs=Math.floor(dayStart.getTime()/1000), re=Math.floor(dayEnd.getTime()/1000);
    return events.filter(function(e){ if(state.slot && e.slot!==state.slot) return false; return e.end>=rs && e.start<=re; })
                 .sort(function(a,b){ return a.start-b.start; });
  }
  function el(tag, attrs, html){ var n=document.createElement(tag); if(attrs) for(var k in attrs){ n.setAttribute(k, attrs[k]); } if(html!==undefined) n.innerHTML=html; return n; }

  function renderMonth(){
    var cont=document.getElementById('sbcal'); cont.innerHTML=''; cont.className='sbcal sb-month';
    var monthStart=startOfMonth(state.date); var firstw=monthStart.getDay()||7;
    var gridStart=new Date(monthStart); gridStart.setDate(gridStart.getDate()-(firstw-1));
    var table=el('table'), thead=el('thead'), thr=el('tr');
    ['Mo','Di','Mi','Do','Fr','Sa','So'].forEach(function(n){ thr.appendChild(el('th',null,n)); });
    thead.appendChild(thr); table.appendChild(thead);
    var tbody=el('tbody'); var day=new Date(gridStart); var today=new Date(); today.setHours(0,0,0,0);
    for(var w=0; w<6; w++){
      var tr=el('tr');
      for(var d=0; d<7; d++){
        var td=el('td');
        if(day.getMonth()!==state.date.getMonth()) td.classList.add('sb-othermonth');
        if(isWeekend(day)) td.classList.add('sb-weekend');
        if(isSameDate(day,today)) td.classList.add('is-today');
        var start=new Date(day.getFullYear(), day.getMonth(), day.getDate(),0,0,0);
        var end  =new Date(day.getFullYear(), day.getMonth(), day.getDate(),23,59,59);
        var box=el('div'); box.appendChild(el('span', {'class':'sb-daynum'}, String(day.getDate())));
        filterEvents(start,end).forEach(function(e){
          var chip=el('span', {'class':'sb-chip '+clsFor(e.slot)}, timeLabel(e.start)+' '+e.title);
          chip.setAttribute('data-tip', e.desc); chip.setAttribute('aria-label', e.desc); chip.tabIndex=0; box.appendChild(chip);
        });
        td.appendChild(box); tr.appendChild(td); day.setDate(day.getDate()+1);
      }
      tbody.appendChild(tr);
    }
    table.appendChild(tbody); cont.appendChild(table);
  }

  function renderWeek(){
    var cont=document.getElementById('sbcal'); cont.innerHTML=''; cont.className='sbcal sb-week';
    var ws=startOfWeek(state.date); var table=el('table'), thead=el('thead'), thr=el('tr'); var today=new Date(); today.setHours(0,0,0,0);
    ['Mo','Di','Mi','Do','Fr','Sa','So'].forEach(function(n,i){ var d=new Date(ws); d.setDate(ws.getDate()+i); thr.appendChild(el('th',null,n+' '+d.getDate()+'.'+(d.getMonth()+1)+'.')); });
    thead.appendChild(thr); table.appendChild(thead);
    var tbody=el('tbody'), tr=el('tr');
    for(var i=0;i<7;i++){
      var d=new Date(ws); d.setDate(ws.getDate()+i);
      var start=new Date(d.getFullYear(), d.getMonth(), d.getDate(),0,0,0);
      var end  =new Date(d.getFullYear(), d.getMonth(), d.getDate(),23,59,59);
      var td=el('td'); if(isWeekend(d)) td.classList.add('sb-weekend'); if(isSameDate(d,today)) td.classList.add('is-today');
      filterEvents(start,end).forEach(function(e){
        var chip=el('div', {'class':'sb-chip '+clsFor(e.slot)}, timeLabel(e.start)+' '+e.title);
        chip.setAttribute('data-tip', e.desc); chip.setAttribute('aria-label', e.desc); chip.tabIndex=0; td.appendChild(chip);
      });
      tr.appendChild(td);
    }
    tbody.appendChild(tr); table.appendChild(tbody); cont.appendChild(table);
  }

  function renderDay(){
    var cont=document.getElementById('sbcal'); cont.innerHTML=''; cont.className='sbcal sb-day';
    var d=new Date(state.date); var start=new Date(d.getFullYear(), d.getMonth(), d.getDate(),0,0,0); var end=new Date(d.getFullYear(), d.getMonth(), d.getDate(),23,59,59);
    var table=el('table'), thead=el('thead'), thr=el('tr'); thr.appendChild(el('th',null, d.getDate()+'.'+(d.getMonth()+1)+'.'+d.getFullYear())); thead.appendChild(thr); table.appendChild(thead);
    var tbody=el('tbody'), tr=el('tr'), td=el('td'); var today=new Date(); today.setHours(0,0,0,0);
    if(isWeekend(d)) td.classList.add('sb-weekend'); if(isSameDate(d,today)) td.classList.add('is-today');
    var evs=filterEvents(start,end); if(evs.length===0){ td.appendChild(document.createTextNode('—')); }
    evs.forEach(function(e){
      var chip=el('div', {'class':'sb-chip '+clsFor(e.slot)}, timeLabel(e.start)+' '+e.title);
      chip.setAttribute('data-tip', e.desc); chip.setAttribute('aria-label', e.desc); chip.tabIndex=0; td.appendChild(chip);
    });
    tr.appendChild(td); tbody.appendChild(tr); table.appendChild(tbody); cont.appendChild(table);
  }

  function render(){
    if(state.view==='week') renderWeek(); else if(state.view==='day') renderDay(); else renderMonth();
    document.getElementById('sb-date').value = formatISO(state.date);
    document.getElementById('sb-month').value = formatISO(state.date).slice(0,7);
    var url=new URL(window.location.href); url.searchParams.set('view', state.view); url.searchParams.set('date', formatISO(state.date)); window.history.replaceState({},'',url.toString());
  }

  document.getElementById('sb-today').addEventListener('click', function(){ state.date=new Date(); render(); });
  document.getElementById('sb-view').addEventListener('change', function(){ state.view=this.value; render(); });
  document.getElementById('sb-slot').addEventListener('change', function(){ state.slot=this.value; render(); });
  document.getElementById('sb-date').addEventListener('change', function(){ var d=parseIsoDate(this.value); if(d){ state.date=d; render(); } });
  document.getElementById('sb-month').addEventListener('change', function(){ var m=this.value; var d=parseIsoDate(m+'-01'); if(d){ state.date=d; render(); } });

  render();
})();
JS;
echo html_writer::tag('script', $js);

// ===== Tooltip-Engine – Akzent immer weiß =====
echo html_writer::tag('script', <<<JS
(function(){
  "use strict";
  var root = document.getElementById('sb-tooltip-root');
  var tip, active = null;

  function ensureTip(){
    if(!tip){
      tip = document.createElement('div');
      tip.className = 'sb-tooltip';
      tip.setAttribute('role','tooltip');
      tip.style.display = 'none';
      root.appendChild(tip);
    }
    return tip;
  }

  function clamp(v, min, max){ return Math.max(min, Math.min(max, v)); }

  function position(anchor){
    if(!tip || !anchor) return;
    tip.textContent = anchor.getAttribute('data-tip') || '';
    tip.style.display = 'block';
    tip.classList.remove('top','bottom','is-visible');

    // <<< Wichtig: Akzent immer weiß, unabhängig vom Chip (keine Blau/Grün-Vererbung)
    tip.style.color = '#fff';

    // messen
    tip.style.visibility = 'hidden';
    var r = anchor.getBoundingClientRect();
    var vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    var vh = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    var margin = 10;
    var tRect = tip.getBoundingClientRect();

    var canAbove = r.top >= (tRect.height + margin + 8);
    var canBelow = (vh - r.bottom) >= (tRect.height + margin + 8);
    var place = canAbove || !canBelow ? 'top' : 'bottom';

    var centerX = r.left + r.width / 2;
    var left = clamp(centerX - tRect.width / 2, 8, vw - tRect.width - 8);
    var arrowX = clamp(centerX - left, 12, tRect.width - 12);
    tip.style.setProperty('--arrow-x', arrowX + 'px');

    var top = (place==='top') ? (r.top - margin - tRect.height) : (r.bottom + margin);
    tip.classList.add(place);

    tip.style.left = Math.round(left) + 'px';
    tip.style.top  = Math.round(top)  + 'px';
    tip.style.visibility = 'visible';
    requestAnimationFrame(function(){ tip.classList.add('is-visible'); });
  }

  function show(anchor){ active = anchor; ensureTip(); position(anchor); }
  function hide(){ if(!tip) return; tip.classList.remove('is-visible'); tip.style.display = 'none'; active = null; }

  document.addEventListener('mouseenter', function(e){ var n=e.target.closest('.sb-chip[data-tip]'); if(n) show(n); }, true);
  document.addEventListener('mouseleave', function(e){ var n=e.target.closest('.sb-chip[data-tip]'); if(n) hide(); }, true);
  document.addEventListener('focusin',  function(e){ var n=e.target.closest('.sb-chip[data-tip]'); if(n) show(n); });
  document.addEventListener('focusout', function(e){ var n=e.target.closest('.sb-chip[data-tip]'); if(n) hide();  });
  document.addEventListener('touchstart', function(e){ var n=e.target.closest('.sb-chip[data-tip]'); if(n){ show(n); setTimeout(hide, 1800); } }, {passive:true});
  window.addEventListener('scroll', function(){ if(active){ position(active); } }, {passive:true});
  window.addEventListener('resize', function(){ if(active){ position(active); } });
})();
JS);

echo $OUTPUT->footer();
