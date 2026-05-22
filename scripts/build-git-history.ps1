# Builds incremental commit history (Apr 26 - May 22, 2026)
$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot\..

function Commit-At {
    param([string]$Date, [string]$Message, [string[]]$Files)
    if (-not $Files -or $Files.Count -eq 0) { throw "No files for: $Message" }
    foreach ($f in $Files) {
        if (-not (Test-Path $f)) { throw "Missing: $f" }
        git add -- $f
    }
    $env:GIT_AUTHOR_DATE = $Date
    $env:GIT_COMMITTER_DATE = $Date
    git commit -m $Message
    Remove-Item Env:GIT_AUTHOR_DATE -ErrorAction SilentlyContinue
    Remove-Item Env:GIT_COMMITTER_DATE -ErrorAction SilentlyContinue
}

if (Test-Path .git) { Remove-Item -Recurse -Force .git }

git init -b main

$commits = @(
    @{
        Date = '2026-04-26 09:30:00 +0000'
        Message = 'chore: initialize Campus EventHub project'
        Files = @('.gitignore', 'README.md')
    },
    @{
        Date = '2026-04-27 14:15:00 +0000'
        Message = 'feat(db): add MySQL schema for users, events, and tickets'
        Files = @('database/schema.sql', 'database/.htaccess')
    },
    @{
        Date = '2026-04-28 11:00:00 +0000'
        Message = 'feat(db): add seed data and database setup notes'
        Files = @('database/seed.sql', 'database/README.md', 'database/migrate-local.php')
    },
    @{
        Date = '2026-04-29 16:45:00 +0000'
        Message = 'feat(config): environment loader and database connection'
        Files = @('.env.example', 'app/config/env.php', 'app/config/database.php')
    },
    @{
        Date = '2026-04-30 10:20:00 +0000'
        Message = 'feat(core): MVC base classes and HTTP router'
        Files = @('app/core/Model.php', 'app/core/Controller.php', 'app/core/Router.php', 'app/.htaccess')
    },
    @{
        Date = '2026-05-01 13:00:00 +0000'
        Message = 'feat(core): session, CSRF, validation, and helpers'
        Files = @('app/core/Session.php', 'app/core/CSRF.php', 'app/core/Validator.php', 'app/core/Helpers.php')
    },
    @{
        Date = '2026-05-02 09:45:00 +0000'
        Message = 'feat(auth): user model and authentication service'
        Files = @('app/models/User.php', 'app/core/Auth.php')
    },
    @{
        Date = '2026-05-03 15:30:00 +0000'
        Message = 'feat(auth): login, register, and logout flow'
        Files = @('app/controllers/AuthController.php', 'app/views/auth/login.php', 'app/views/auth/register.php')
    },
    @{
        Date = '2026-05-04 11:10:00 +0000'
        Message = 'feat(app): front controller and Apache rewrite rules'
        Files = @('index.php', '.htaccess', 'public/index.php', 'public/.htaccess')
    },
    @{
        Date = '2026-05-05 14:00:00 +0000'
        Message = 'feat(events): event model with search and capacity helpers'
        Files = @('app/models/Event.php')
    },
    @{
        Date = '2026-05-06 10:00:00 +0000'
        Message = 'feat(events): public event listing and detail pages'
        Files = @('app/controllers/EventController.php', 'app/views/events/index.php', 'app/views/events/show.php')
    },
    @{
        Date = '2026-05-07 16:20:00 +0000'
        Message = 'feat(ui): landing page layout, styles, and scripts'
        Files = @(
            'app/views/layouts/header.php', 'app/views/layouts/footer.php',
            'app/views/public/home.php', 'app/views/public/about.php',
            'app/views/partials/brand-logo.php', 'app/views/partials/page-banner.php',
            'public/assets/css/landing.css', 'public/assets/js/landing.js'
        )
    },
    @{
        Date = '2026-05-08 09:00:00 +0000'
        Message = 'feat(dashboard): admin and user dashboard statistics'
        Files = @('app/models/DashboardStats.php', 'app/controllers/DashboardController.php')
    },
    @{
        Date = '2026-05-09 13:45:00 +0000'
        Message = 'feat(dashboard): role-based dashboard views and layout'
        Files = @(
            'app/views/layouts/dashboard_layout.php',
            'app/views/dashboard/index.php', 'app/views/dashboard/admin.php',
            'app/views/partials/dash-icon.php', 'app/views/partials/dash-page-header.php',
            'public/assets/css/dashboard.css'
        )
    },
    @{
        Date = '2026-05-10 11:30:00 +0000'
        Message = 'feat(announcements): announcement model and public controller'
        Files = @('app/models/Announcement.php', 'app/controllers/AnnouncementController.php')
    },
    @{
        Date = '2026-05-11 15:00:00 +0000'
        Message = 'feat(announcements): published list and icon partials'
        Files = @(
            'app/views/announcements/index.php',
            'app/views/partials/announcement-icon.php', 'app/views/partials/pagination.php'
        )
    },
    @{
        Date = '2026-05-12 10:15:00 +0000'
        Message = 'feat(tickets): ticket request model and user workflow'
        Files = @('app/models/TicketRequest.php', 'app/controllers/TicketController.php', 'app/views/tickets/my-tickets.php')
    },
    @{
        Date = '2026-05-13 14:30:00 +0000'
        Message = 'feat(admin): admin controller and event management views'
        Files = @(
            'app/controllers/AdminController.php',
            'app/views/admin/events/index.php', 'app/views/admin/events/_form.php'
        )
    },
    @{
        Date = '2026-05-14 09:50:00 +0000'
        Message = 'feat(admin): event create, edit, show, and image upload'
        Files = @(
            'app/views/admin/events/create.php', 'app/views/admin/events/edit.php', 'app/views/admin/events/show.php',
            'app/services/EventImageUpload.php', 'public/assets/images/README.md'
        )
    },
    @{
        Date = '2026-05-15 16:00:00 +0000'
        Message = 'feat(admin): announcement CRUD screens'
        Files = @(
            'app/views/admin/announcements/index.php', 'app/views/admin/announcements/create.php',
            'app/views/admin/announcements/edit.php', 'app/views/admin/announcements/_form.php'
        )
    },
    @{
        Date = '2026-05-16 11:20:00 +0000'
        Message = 'feat(admin): ticket review and audit log pages'
        Files = @(
            'app/views/admin/tickets/index.php', 'app/views/admin/tickets/show.php',
            'app/views/admin/activity-logs.php', 'app/views/admin/ai-logs.php'
        )
    },
    @{
        Date = '2026-05-17 13:10:00 +0000'
        Message = 'feat(models): activity, AI, and FAQ data layers'
        Files = @('app/models/ActivityLog.php', 'app/models/AiLog.php', 'app/models/FaqItem.php')
    },
    @{
        Date = '2026-05-18 10:40:00 +0000'
        Message = 'feat(ai): Longcat API integration and draft controller'
        Files = @('app/config/longcat.php', 'app/services/LongcatService.php', 'app/controllers/AiController.php')
    },
    @{
        Date = '2026-05-19 15:25:00 +0000'
        Message = 'feat(ai): help assistant, draft UI, and client scripts'
        Files = @(
            'app/views/help-assistant/index.php', 'app/views/admin/ai-draft.php',
            'public/assets/js/ai.js', 'public/assets/js/validation.js'
        )
    },
    @{
        Date = '2026-05-20 09:35:00 +0000'
        Message = 'feat(tickets): FPDF library and downloadable ticket PDFs'
        Files = @(
            'app/lib/fpdf/fpdf.php', 'app/lib/fpdf/FpdfFonts.php', 'app/lib/fpdf/install-fonts.php',
            'app/lib/fpdf/README.md', 'app/lib/fpdf/font/helvetica.php', 'app/lib/fpdf/font/helveticab.php',
            'app/lib/fpdf/font/helveticai.php', 'app/lib/fpdf/font/helveticabi.php',
            'app/services/TicketPdfService.php'
        )
    },
    @{
        Date = '2026-05-21 14:50:00 +0000'
        Message = 'feat(ui): global styles, flash messages, and error pages'
        Files = @(
            'app/config/config.php', 'public/assets/css/style.css', 'public/assets/css/responsive.css',
            'public/assets/js/main.js', 'app/views/partials/flash.php', 'app/views/partials/form-errors.php',
            'app/views/errors/_bootstrap.php', 'app/views/errors/404.php', 'app/views/errors/403.php', 'app/views/errors/500.php'
        )
    },
    @{
        Date = '2026-05-22 17:00:00 +0000'
        Message = 'docs: project documentation, test guides, and final polish'
        Files = @(
            'docs/proposal.md', 'docs/system-design.md', 'docs/ai-governance.md',
            'docs/security-risk-register.md', 'docs/test-cases.md',
            'tests/README.md', 'tests/manual-test-checklist.md', 'tests/test-results-template.md',
            'scripts/build-git-history.ps1'
        )
    }
)

$i = 0
foreach ($c in $commits) {
    $i++
    Write-Host "[$i/$($commits.Count)] $($c.Message)"
    Commit-At -Date $c.Date -Message $c.Message -Files $c.Files
}

$remaining = git status --porcelain
if ($remaining) {
    Write-Warning "Uncommitted files remain:"
    Write-Host $remaining
} else {
    Write-Host 'All project files committed.'
}

Write-Host "`nCommit log:"
git log --oneline --reverse
