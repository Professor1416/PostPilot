@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="pp-main">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
        <div>
            <h1 class="pp-page-title">Content Calendar</h1>
            <p class="pp-page-sub">Click any date to schedule a post. Click a post to view details.</p>
        </div>
        <button class="pp-btn pp-btn-primary" onclick="openComposer()">+ New Post</button>
    </div>

    {{-- Calendar --}}
    <div class="pp-card" style="padding:16px;">
        <div id="calendar"></div>
    </div>

    {{-- Legend --}}
    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-top:12px;">
        @foreach(['#E1306C' => 'Instagram', '#27AE60' => 'Published', '#C0392B' => 'Failed', 'rgba(255,107,0,0.5)' => 'Festival'] as $color => $label)
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#666;">
                <span style="width:10px;height:10px;border-radius:50%;background:{{ $color }};display:inline-block;"></span>
                {{ $label }}
            </div>
        @endforeach
    </div>
</div>

{{-- Post Composer Drawer --}}
<div class="pp-overlay" id="composerOverlay" onclick="if(event.target===this)closeComposer()">
    <div class="pp-drawer">
        <div class="pp-drawer-top">
            <span class="pp-drawer-title">New Post</span>
            <button class="pp-close-btn" onclick="closeComposer()">✕</button>
        </div>
        <div class="pp-drawer-body">

            {{-- Platform --}}
            <label class="pp-label">Platform</label>
            <div style="display:flex;gap:10px;margin-bottom:16px;">
                <button type="button" id="btn-ig" onclick="togglePlatform('instagram')"
                    style="flex:1;padding:10px;background:#080810;border:2px solid #22223A;border-radius:10px;color:#666;font-size:13px;font-weight:600;cursor:pointer;">
                    📸 Instagram
                </button>
            </div>

            {{-- Account --}}
            <label class="pp-label">Account</label>
            @if($accounts->isEmpty())
                <div style="background:rgba(255,107,0,0.08);border:1px solid rgba(255,107,0,0.2);border-radius:10px;padding:12px;font-size:12px;color:#888;margin-bottom:16px;">
                    No accounts connected. Go to <a href="{{ route('dashboard.accounts') }}" style="color:#FF6B00;">Accounts</a> to connect Instagram.
                </div>
            @else
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;" id="accountsList">
                    @foreach($accounts as $account)
                        <div id="acc-{{ $account->id }}" onclick="toggleAccount({{ $account->id }})"
                            style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#080810;border:1px solid #22223A;border-radius:20px;color:#666;font-size:12px;cursor:pointer;">
                            @if($account->profile_picture_url)
                                <img src="{{ $account->profile_picture_url }}" style="width:20px;height:20px;border-radius:50%;" alt="">
                            @endif
                            @{{ $account->account_name }}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- AI Toggle --}}
            <div class="pp-toggle-wrap">
                <span class="pp-toggle-label">Generate with AI</span>
                <input type="checkbox" class="pp-toggle" id="aiToggle" checked onchange="toggleAI(this.checked)">
            </div>

            <div id="aiBox" style="background:#080810;border:1px solid #18182A;border-radius:12px;padding:16px;margin-bottom:16px;">
                <label class="pp-label">Business Name</label>
                <input class="pp-input" id="bizName" placeholder="e.g. Sharma Medical Store">

                <label class="pp-label">Business Type</label>
                <input class="pp-input" id="bizType" placeholder="e.g. Pharmacy, Restaurant, Salon">

                <label class="pp-label">Your Offer / Message</label>
                <textarea class="pp-textarea" id="offer" placeholder="e.g. 20% off on all medicines this weekend"></textarea>

                <div style="display:flex;gap:14px;">
                    <div style="flex:1;">
                        <label class="pp-label">Language</label>
                        <select class="pp-select" id="language">
                            <option value="hinglish">Hinglish</option>
                            <option value="english">English</option>
                            <option value="hindi">Hindi</option>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label class="pp-label">Festival</label>
                        <select class="pp-select" id="festival">
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>

                <label class="pp-label">Tone</label>
                <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
                    @foreach(['friendly','professional','exciting','urgent'] as $t)
                        <button type="button" class="tone-btn" data-tone="{{ $t }}" onclick="setTone('{{ $t }}')"
                            style="padding:6px 14px;background:#0A0A14;border:1px solid #22223A;border-radius:20px;color:#666;font-size:12px;cursor:pointer;">
                            {{ ucfirst($t) }}
                        </button>
                    @endforeach
                </div>

                <button type="button" class="pp-btn" onclick="generateCaption()"
                    style="width:100%;background:rgba(255,107,0,0.15);border:1px solid rgba(255,107,0,0.4);color:#FF6B00;font-family:'DM Sans',sans-serif;" id="genBtn">
                    ⚡ Generate Caption
                </button>
            </div>

            {{-- Caption --}}
            <label class="pp-label">Caption</label>
            <textarea class="pp-textarea" id="caption" style="min-height:120px;"
                placeholder="Write your caption or generate with AI above..." oninput="updateCharCount()"></textarea>
            <div style="font-size:11px;color:#333;text-align:right;margin-top:-10px;margin-bottom:14px;" id="charCount">0 / 2,200</div>

            {{-- Image --}}
            <label class="pp-label">Image (Required for Instagram)</label>
            <div id="uploadZone" onclick="document.getElementById('imageInput').click()"
                style="border:2px dashed #22223A;border-radius:12px;padding:24px;display:flex;flex-direction:column;align-items:center;gap:8px;cursor:pointer;margin-bottom:16px;">
                <span style="font-size:24px;">📷</span>
                <span style="font-size:13px;color:#666;">Tap to upload image (JPG/PNG, max 8MB)</span>
            </div>
            <div id="imagePreview" style="display:none;position:relative;margin-bottom:16px;">
                <img id="previewImg" style="width:100%;border-radius:10px;max-height:200px;object-fit:cover;" alt="">
                <button onclick="removeImage()" style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,0.7);border:none;border-radius:6px;color:#fff;font-size:12px;padding:4px 10px;cursor:pointer;">✕ Remove</button>
            </div>
            <input type="file" id="imageInput" accept="image/*" style="display:none;" onchange="handleImage(this)">

            {{-- Schedule --}}
            <label class="pp-label">Schedule Date & Time (IST)</label>
            <input type="datetime-local" class="pp-input" id="scheduledAt">

            {{-- Submit --}}
            <button type="button" class="pp-btn pp-btn-primary" onclick="submitPost()" id="submitBtn"
                style="width:100%;margin-top:8px;margin-bottom:8px;">Schedule Post ⚡</button>
            <button type="button" class="pp-btn pp-btn-secondary" onclick="closeComposer()" style="width:100%;">Cancel</button>
        </div>
    </div>
</div>

{{-- Post Detail Drawer --}}
<div class="pp-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetail()">
    <div class="pp-drawer">
        <div class="pp-drawer-top">
            <span class="pp-drawer-title">Post Details</span>
            <button class="pp-close-btn" onclick="closeDetail()">✕</button>
        </div>
        <div class="pp-drawer-body" id="detailBody">
            <div style="color:#555;font-size:13px;">Loading...</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const FESTIVALS_URL = '{{ route("festivals") }}';
const GENERATE_URL  = '{{ route("generate") }}';
const CALENDAR_URL  = '{{ route("api.calendar") }}';

let selectedPlatforms  = ['instagram'];
let selectedAccountIds = [];
let selectedTone       = 'friendly';
let imageFile          = null;
let currentYear        = new Date().getFullYear();
let currentMonth       = new Date().getMonth() + 1;
let calendar;

// ─── Calendar init ────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    await loadFestivals();
    initCalendar();

    // Set default scheduled time (+1 hour)
    const d = new Date(Date.now() + 3600000);
    d.setSeconds(0, 0);
    document.getElementById('scheduledAt').value = d.toISOString().slice(0, 16);

    // Default first account selected
    @if($accounts->isNotEmpty())
        toggleAccount({{ $accounts->first()->id }});
    @endif

    setTone('friendly');
    togglePlatform('instagram');
});

async function loadFestivals() {
    try {
        const data = await fetch(FESTIVALS_URL).then(r => r.json());
        const sel  = document.getElementById('festival');
        (data.festivals || []).forEach(f => {
            const opt = document.createElement('option');
            opt.value       = f.name;
            opt.textContent = `${f.emoji || ''} ${f.name}`;
            sel.appendChild(opt);
        });
    } catch {}
}

function initCalendar() {
    const el = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
        dateClick: info => openComposer(info.dateStr),
        eventClick: info => {
            if (info.event.extendedProps.isFestival) {
                openComposer(info.event.startStr);
            } else {
                openDetail(info.event.extendedProps.postId);
            }
        },
        datesSet: info => {
            const d = new Date(info.view.currentStart);
            d.setDate(15);
            currentYear  = d.getFullYear();
            currentMonth = d.getMonth() + 1;
            loadCalendarData();
        },
    });
    calendar.render();
}

async function loadCalendarData() {
    try {
        const data = await fetch(`${CALENDAR_URL}?year=${currentYear}&month=${currentMonth}`).then(r => r.json());

        calendar.getEvents().forEach(e => e.remove());

        Object.entries(data.calendar || {}).forEach(([date, posts]) => {
            posts.forEach(post => {
                calendar.addEvent({
                    id:    post.post_id,
                    title: post.caption_snippet || 'Post',
                    date,
                    backgroundColor: post.status === 'published' ? '#27AE60' : post.status === 'failed' ? '#C0392B' : '#E1306C',
                    borderColor: 'transparent',
                    extendedProps: { postId: post.post_id, post },
                });
            });
        });
    } catch (e) { console.error('Calendar load failed', e); }
}

// ─── Composer ─────────────────────────────────────────────────────────────────
function openComposer(date = null) {
    if (date) {
        const d = new Date(date + 'T19:00');
        document.getElementById('scheduledAt').value = d.toISOString().slice(0, 16);
    }
    document.getElementById('composerOverlay').classList.add('open');
}

function closeComposer() {
    document.getElementById('composerOverlay').classList.remove('open');
}

function togglePlatform(platform) {
    const btn = document.getElementById('btn-' + platform);
    const idx = selectedPlatforms.indexOf(platform);
    if (idx > -1) {
        if (selectedPlatforms.length === 1) return;
        selectedPlatforms.splice(idx, 1);
        btn.style.background   = '#080810';
        btn.style.borderColor  = '#22223A';
        btn.style.color        = '#666';
    } else {
        selectedPlatforms.push(platform);
        btn.style.background  = 'rgba(225,48,108,0.14)';
        btn.style.borderColor = '#E1306C';
        btn.style.color       = '#E1306C';
    }
}

function toggleAccount(id) {
    const el  = document.getElementById('acc-' + id);
    const idx = selectedAccountIds.indexOf(id);
    if (idx > -1) {
        selectedAccountIds.splice(idx, 1);
        el.style.background  = '#080810';
        el.style.borderColor = '#22223A';
        el.style.color       = '#666';
    } else {
        selectedAccountIds.push(id);
        el.style.background  = 'rgba(225,48,108,0.12)';
        el.style.borderColor = '#E1306C';
        el.style.color       = '#E1306C';
    }
}

function setTone(tone) {
    selectedTone = tone;
    document.querySelectorAll('.tone-btn').forEach(b => {
        const on = b.dataset.tone === tone;
        b.style.background   = on ? 'rgba(255,107,0,0.1)' : '#0A0A14';
        b.style.borderColor  = on ? '#FF6B00' : '#22223A';
        b.style.color        = on ? '#FF6B00' : '#666';
    });
}

function toggleAI(on) {
    document.getElementById('aiBox').style.display = on ? 'block' : 'none';
}

function updateCharCount() {
    const len = document.getElementById('caption').value.length;
    document.getElementById('charCount').textContent = `${len} / 2,200`;
}

async function generateCaption() {
    const btn = document.getElementById('genBtn');
    btn.textContent = 'Generating...';
    btn.disabled = true;

    try {
        const data = await apiPost(GENERATE_URL, {
            business_name: document.getElementById('bizName').value,
            business_type: document.getElementById('bizType').value,
            offer:         document.getElementById('offer').value,
            content_type:  selectedPlatforms.includes('instagram') ? 'instagram' : 'facebook',
            language:      document.getElementById('language').value,
            festival:      document.getElementById('festival').value,
            tone:          selectedTone,
        });

        if (data.result) {
            document.getElementById('caption').value = data.result;
            updateCharCount();
            showToast('Caption generated!');
        } else {
            showToast(data.error || 'Generation failed', 'error');
        }
    } catch {
        showToast('Generation failed. Try again.', 'error');
    }

    btn.textContent = '⚡ Generate Caption';
    btn.disabled = false;
}

function handleImage(input) {
    const file = input.files?.[0];
    if (!file) return;
    if (file.size > 8 * 1024 * 1024) { showToast('Image must be under 8MB', 'error'); return; }
    imageFile = file;
    const reader = new FileReader();
    reader.onloadend = () => {
        document.getElementById('previewImg').src = reader.result;
        document.getElementById('uploadZone').style.display  = 'none';
        document.getElementById('imagePreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    imageFile = null;
    document.getElementById('imageInput').value = '';
    document.getElementById('uploadZone').style.display   = 'block';
    document.getElementById('imagePreview').style.display = 'none';
}

async function submitPost() {
    const caption     = document.getElementById('caption').value.trim();
    const scheduledAt = document.getElementById('scheduledAt').value;

    if (!caption)     { showToast('Caption is required', 'error'); return; }
    if (!scheduledAt) { showToast('Schedule date and time required', 'error'); return; }
    if (selectedAccountIds.length === 0) { showToast('Select at least one account', 'error'); return; }

    const btn = document.getElementById('submitBtn');
    btn.textContent = 'Scheduling...';
    btn.disabled    = true;

    try {
        const formData = new FormData();
        selectedPlatforms.forEach(p => formData.append('platforms[]', p));
        selectedAccountIds.forEach(a => formData.append('connected_account_ids[]', a));
        formData.append('caption',      caption);
        formData.append('scheduled_at', new Date(scheduledAt).toISOString());
        formData.append('language',     document.getElementById('language').value);
        formData.append('festival',     document.getElementById('festival').value);
        formData.append('ai_generated', document.getElementById('aiToggle').checked ? '1' : '0');
        formData.append('_token',       window.CSRF_TOKEN);
        if (imageFile) formData.append('image', imageFile);

        const res  = await fetch('{{ route("posts.store") }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (res.ok) {
            showToast('Post scheduled!');
            closeComposer();
            loadCalendarData();
        } else if (data.upgrade) {
            showToast('Post quota reached. Upgrade your plan.', 'warning');
            window.location.href = '{{ route("dashboard.pricing") }}';
        } else {
            showToast(data.error || 'Failed to schedule post', 'error');
        }
    } catch {
        showToast('Failed to schedule post', 'error');
    }

    btn.textContent = 'Schedule Post ⚡';
    btn.disabled    = false;
}

// ─── Post Detail ──────────────────────────────────────────────────────────────
async function openDetail(postId) {
    document.getElementById('detailOverlay').classList.add('open');
    document.getElementById('detailBody').innerHTML = '<div style="color:#555;font-size:13px;padding:20px;">Loading...</div>';

    try {
        const post = await fetch(`/posts/${postId}`, {
            headers: { 'Accept': 'application/json' }
        }).then(r => r.json());

        const statusClass = {
            scheduled: 'pp-badge-scheduled', published: 'pp-badge-published',
            failed: 'pp-badge-failed', draft: 'pp-badge-draft',
        }[post.status] || 'pp-badge-draft';

        document.getElementById('detailBody').innerHTML = `
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
                <span class="pp-badge ${statusClass}">${post.status}</span>
                ${(post.platforms||[]).map(p => `<span class="pp-badge" style="background:rgba(225,48,108,0.12);color:#E1306C;">📸 ${p}</span>`).join('')}
                ${post.ai_generated ? '<span class="pp-badge" style="background:rgba(155,89,182,0.1);color:#9B59B6;">AI</span>' : ''}
            </div>
            ${post.image_url ? `<img src="${post.image_url}" style="width:100%;border-radius:12px;max-height:220px;object-fit:cover;margin-bottom:16px;" alt="">` : ''}
            <div style="font-size:11px;color:#555;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:7px;">Caption</div>
            <div style="font-size:14px;color:#BEBECE;line-height:1.7;white-space:pre-wrap;background:#080810;border:1px solid #1E1E2E;border-radius:10px;padding:14px;margin-bottom:16px;">${post.caption || '—'}</div>
            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #0D0D16;">
                    <span style="font-size:12px;color:#555;">Scheduled for</span>
                    <span style="font-size:12px;color:#AAA;font-weight:500;">${post.scheduled_at_ist}</span>
                </div>
                ${post.festival ? `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #0D0D16;"><span style="font-size:12px;color:#555;">Festival</span><span style="font-size:12px;color:#AAA;">${post.festival}</span></div>` : ''}
                ${post.failure_reason ? `<div style="padding:8px;background:rgba(192,57,43,0.08);border:1px solid rgba(192,57,43,0.3);border-radius:8px;font-size:12px;color:#C0392B;margin-top:8px;">${post.failure_reason}</div>` : ''}
            </div>
            ${['scheduled','draft'].includes(post.status) ? `
                <button onclick="deletePost(${post.id})" class="pp-btn pp-btn-danger" style="width:100%;">🗑 Delete Post</button>
            ` : ''}
            ${post.status === 'failed' ? `
                <div style="background:#080810;border:1px solid #18182A;border-radius:12px;padding:16px;margin-top:8px;">
                    <div style="font-size:13px;color:#888;margin-bottom:10px;">Reschedule this post</div>
                    <input type="datetime-local" class="pp-input" id="rescheduleTime" min="${new Date().toISOString().slice(0,16)}">
                    <button onclick="reschedulePost(${post.id})" class="pp-btn pp-btn-primary" style="width:100%;">↻ Confirm Reschedule</button>
                </div>
            ` : ''}
        `;
    } catch {
        document.getElementById('detailBody').innerHTML = '<div style="color:#C0392B;font-size:13px;">Failed to load post.</div>';
    }
}

function closeDetail() {
    document.getElementById('detailOverlay').classList.remove('open');
}

async function deletePost(id) {
    if (!confirm('Delete this post? This cannot be undone.')) return;
    const data = await apiDelete(`/posts/${id}`);
    if (data.success) {
        showToast('Post deleted');
        closeDetail();
        loadCalendarData();
    } else {
        showToast(data.error || 'Delete failed', 'error');
    }
}

async function reschedulePost(id) {
    const time = document.getElementById('rescheduleTime')?.value;
    if (!time) { showToast('Select a new time', 'error'); return; }
    const data = await apiPost(`/posts/${id}/reschedule`, { scheduled_at: new Date(time).toISOString() });
    if (data.post_id) {
        showToast('Post rescheduled!');
        closeDetail();
        loadCalendarData();
    } else {
        showToast(data.error || 'Reschedule failed', 'error');
    }
}
</script>
@endpush
