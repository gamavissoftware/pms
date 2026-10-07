<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php /* Shared look for the Accounts finance pages: running DF control,
        upcoming payments and receivables. Edit here, not per page. */ ?>
    <style>
        :root{
            --fin-ink:#12233a; --fin-muted:#5d7189; --fin-line:#dce5f0;
            --fin-surface:#ffffff; --fin-bg:#f3f7fb; --fin-brand:#0f4fb7;
            --fin-green:#0a8f5b; --fin-amber:#b8760a; --fin-red:#c22f3d;
            --fin-slate:#8a99ab;
        }
        body{ background:var(--fin-bg); font-family:'Plus Jakarta Sans',sans-serif; color:var(--fin-ink); }
        .fin-shell{ padding:22px 14px 60px; }
        .fin-card{ background:var(--fin-surface); border:1px solid var(--fin-line); border-radius:14px;
                   box-shadow:0 1px 2px rgba(18,35,58,.05); }

        .fin-hero{ padding:24px 26px; margin-bottom:18px; }
        .fin-kicker{ display:inline-block; font-size:11px; font-weight:700; letter-spacing:.09em;
                     text-transform:uppercase; color:var(--fin-brand); background:#e8f0fd;
                     padding:5px 11px; border-radius:20px; }
        .fin-title{ font-size:28px; font-weight:800; margin:12px 0 8px; line-height:1.2; }
        .fin-lede{ font-size:14px; color:var(--fin-muted); max-width:70ch; line-height:1.6; }
        .fin-chip-row{ margin-top:14px; display:flex; flex-wrap:wrap; gap:8px; }
        .fin-chip{ font-size:12px; font-weight:600; color:var(--fin-muted); background:#eef3f9;
                   border:1px solid var(--fin-line); border-radius:8px; padding:6px 11px; }
        .fin-hero-actions{ display:flex; flex-direction:column; gap:9px; }
        .fin-btn{ display:block; text-align:center; font-size:13px; font-weight:700; padding:11px 14px;
                  border-radius:9px; border:1px solid var(--fin-line); background:#f7fafd;
                  color:var(--fin-ink); text-decoration:none; }
        .fin-btn:hover{ background:#eef4fb; color:var(--fin-ink); text-decoration:none; }
        .fin-btn.primary{ background:var(--fin-brand); border-color:var(--fin-brand); color:#fff; }
        .fin-btn.primary:hover{ background:#0c429b; color:#fff; }

        .fin-section-title{ font-size:16px; font-weight:800; margin:0 0 5px; }
        .fin-section-copy{ font-size:13px; color:var(--fin-muted); line-height:1.6; }

        .fin-filter{ padding:18px 22px; margin-bottom:18px; }
        .fin-filter-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr));
                          gap:14px; align-items:end; margin-top:14px; }
        .fin-label{ display:block; font-size:11px; font-weight:700; letter-spacing:.06em;
                    text-transform:uppercase; color:var(--fin-muted); margin-bottom:6px; }
        .fin-filter .form-control{ height:40px; border-radius:9px; border-color:var(--fin-line); font-size:13px; }
        .fin-filter-actions{ display:flex; gap:9px; }
        .fin-filter-actions .fin-btn{ flex:1; }

        .fin-kpi-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(215px,1fr));
                       gap:13px; margin-bottom:18px; }
        .fin-kpi{ padding:17px 18px; }
        .fin-kpi-label{ font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase;
                        color:var(--fin-muted); }
        .fin-kpi-value{ font-size:23px; font-weight:800; margin:9px 0 6px; letter-spacing:-.02em; }
        .fin-kpi-note{ font-size:12px; color:var(--fin-muted); line-height:1.5; }
        .fin-kpi.headline{ border-color:#f0c98a; background:linear-gradient(180deg,#fffaf0,#fff); }
        .fin-kpi.headline .fin-kpi-value{ color:var(--fin-amber); }
        .fin-kpi.danger .fin-kpi-value{ color:var(--fin-red); }
        .fin-kpi.good .fin-kpi-value{ color:var(--fin-green); }

        .fin-panel-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(310px,1fr));
                         gap:13px; margin-bottom:18px; }
        .fin-panel{ padding:19px 20px; }
        .fin-list{ margin-top:13px; display:flex; flex-direction:column; gap:9px; }
        .fin-item{ border:1px solid var(--fin-line); border-left-width:3px; border-radius:9px; padding:11px 13px; }
        .fin-item.red{ border-left-color:var(--fin-red); }
        .fin-item.amber{ border-left-color:var(--fin-amber); }
        .fin-item.slate{ border-left-color:var(--fin-slate); }
        .fin-item-top{ display:flex; justify-content:space-between; gap:10px; align-items:baseline; }
        .fin-item-title{ font-size:13px; font-weight:700; }
        .fin-item-amount{ font-size:13px; font-weight:800; white-space:nowrap; }
        .fin-item-copy{ font-size:12px; color:var(--fin-muted); margin-top:4px; line-height:1.5; }
        .fin-bullets{ margin:13px 0 0; padding-left:18px; }
        .fin-bullets li{ font-size:13px; color:var(--fin-muted); line-height:1.65; margin-bottom:7px; }
        .fin-empty{ font-size:13px; color:var(--fin-muted); text-align:center; padding:26px 12px;
                    border:1px dashed var(--fin-line); border-radius:9px; margin-top:13px; }

        .fin-report{ padding:19px 20px 26px; }
        .fin-toolbar{ display:flex; flex-wrap:wrap; justify-content:space-between; gap:14px;
                      align-items:flex-end; margin-bottom:16px; }
        .fin-toolbar-right{ display:flex; gap:10px; align-items:end; }
        .fin-select{ height:38px; border-radius:9px; border:1px solid var(--fin-line);
                     font-size:13px; padding:0 10px; background:#fff; min-width:175px; }

        table.fin-table{ width:100%; font-size:12.5px; }
        table.fin-table thead th{ background:#eef3f9; border-bottom:1px solid var(--fin-line)!important;
                                  font-size:10.5px; font-weight:800; letter-spacing:.06em;
                                  text-transform:uppercase; color:var(--fin-muted); padding:11px 9px!important;
                                  vertical-align:middle; }
        table.fin-table tbody td{ border-top:1px solid var(--fin-line)!important; padding:13px 9px!important;
                                  vertical-align:top; }
        table.fin-table tbody tr.row-overdue{ background:#fff6f6; }
        table.fin-table tbody tr.row-unbilled{ background:#fffcf3; }
        table.fin-table tbody tr.row-config{ background:#f6f7fa; }

        .fin-idx{ display:inline-flex; width:25px; height:25px; border-radius:7px; background:#eef3f9;
                  color:var(--fin-muted); font-size:11px; font-weight:800;
                  align-items:center; justify-content:center; }
        .cell-title{ font-size:13px; font-weight:700; line-height:1.35; }
        .cell-copy{ font-size:12px; color:var(--fin-muted); line-height:1.55; margin-top:3px; }
        .tag{ display:inline-block; font-size:10.5px; font-weight:700; border-radius:6px;
              padding:3px 7px; background:#eef3f9; color:var(--fin-muted); margin:3px 3px 0 0; }
        .tag.export{ background:#eaf3ff; color:var(--fin-brand); }

        .pill{ display:inline-block; font-size:10.5px; font-weight:800; letter-spacing:.04em;
               text-transform:uppercase; border-radius:20px; padding:4px 10px; }
        .pill.overdue{ background:#fdeaec; color:var(--fin-red); }
        .pill.unbilled{ background:#fdf3e2; color:var(--fin-amber); }
        .pill.config{ background:#eef1f5; color:#5a6a7d; }
        .pill.nopo{ background:#fdeaec; color:var(--fin-red); }
        .pill.watch{ background:#e9f1fe; color:var(--fin-brand); }
        .pill.collected{ background:#e4f6ee; color:var(--fin-green); }
        .pill.ontrack{ background:#eef3f9; color:var(--fin-muted); }
        .pill.claimable{ background:#e4f6ee; color:var(--fin-green); }
        .pill.approval{ background:#e9f1fe; color:var(--fin-brand); }
        .pill.slipped{ background:#fdeaec; color:var(--fin-red); }
        .pill.planned{ background:#eef3f9; color:var(--fin-muted); }
        .pill.unmapped{ background:#eef1f5; color:#5a6a7d; }
        .pill.due{ background:#fdeaec; color:var(--fin-red); }
        .pill.not_due{ background:#e9f1fe; color:var(--fin-brand); }
        .pill.received{ background:#e4f6ee; color:var(--fin-green); }
        .pill.hold{ background:#fdf3e2; color:var(--fin-amber); }
        .pill.not_updated{ background:#eef1f5; color:#5a6a7d; }

        .money-grid{ display:grid; grid-template-columns:1fr 1fr; gap:7px 12px; }
        .money-stat .m-label{ font-size:10px; font-weight:700; letter-spacing:.05em;
                              text-transform:uppercase; color:var(--fin-muted); }
        .money-stat .m-value{ font-size:12.5px; font-weight:700; }
        .money-stat.ready .m-value{ color:var(--fin-amber); }
        .money-stat.overdue .m-value{ color:var(--fin-red); }
        .money-stat.received .m-value{ color:var(--fin-green); }

        .flow{ display:flex; height:7px; border-radius:5px; overflow:hidden; margin:11px 0 7px;
               background:#eef1f5; }
        .flow span{ display:block; height:100%; }
        .flow .received{ background:var(--fin-green); }
        .flow .unpaid{ background:var(--fin-brand); }
        .flow .ready{ background:#e2a13b; }
        .flow .pipeline{ background:#cdd7e3; }
        .flow-key{ display:flex; flex-wrap:wrap; gap:8px; font-size:10.5px; color:var(--fin-muted); }
        .flow-key i{ display:inline-block; width:8px; height:8px; border-radius:2px; margin-right:4px; }

        .ms-card{ border:1px solid var(--fin-line); border-radius:9px; padding:9px 11px; margin-bottom:7px; }
        .ms-head{ display:flex; justify-content:space-between; gap:9px; align-items:baseline; }
        .ms-name{ font-size:12px; font-weight:700; }
        .ms-meta{ font-size:11px; color:var(--fin-muted); margin-top:3px; }

        .ledger-box{ border:1px solid var(--fin-line); border-radius:9px; padding:10px 12px; background:#fbfdff; }
        .ledger-empty{ font-size:11.5px; color:var(--fin-muted); line-height:1.5; }
        .issue-list{ margin:9px 0 0; padding-left:16px; }
        .issue-list li{ font-size:11.5px; color:#8a5a12; line-height:1.55; margin-bottom:4px; }

        .dataTables_wrapper .dt-buttons{ margin-bottom:12px; }
        .dataTables_wrapper .btn{ font-size:12px; font-weight:700; border-radius:8px;
                                  border:1px solid var(--fin-line); background:#f7fafd; color:var(--fin-ink); }
        .modal-note{ font-size:12px; color:var(--fin-muted); background:#f3f7fb;
                     border:1px solid var(--fin-line); border-radius:9px; padding:11px 13px; line-height:1.55; }

        @media (max-width:991px){
            .fin-hero,.fin-filter,.fin-report,.fin-panel{ padding:18px 16px; }
            .fin-title{ font-size:23px; }
            .money-grid{ grid-template-columns:1fr; }
        }
    </style>
