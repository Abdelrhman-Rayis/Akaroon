# Akaroon Demo Video Script — Remotion

A 2-minute product demo showcasing the redesigned **Akaroon digital heritage library** with new features: progressive search with relevance sorting, three search modes (عادي/دلالي/عميق), OCR-powered deep search, and styled markdown viewer.

---

## Video Structure (120 seconds total)

| Duration | Scene | Content |
|----------|-------|---------|
| 0-8s | Intro Slide | Title + tagline |
| 8-20s | Homepage Tour | Hero search, category grid |
| 20-35s | Normal Search (عادي) | Type "تعليم", see instant results |
| 35-50s | Semantic Search (دلالي) | Toggle mode, see synonym expansion |
| 50-65s | Deep Search (عميق) | Toggle mode, see OCR snippets with highlights |
| 65-90s | Skeleton Loading Demo | Show progressive section loading, relevance sorting |
| 90-110s | OCR Viewer | Click snippet, styled markdown reader opens |
| 110-120s | Outro | Call-to-action + credits |

---

## Scene Details

### Scene 1: Intro Slide (0-8s)
**Duration:** 8 seconds

**Visuals:**
- Black/dark teal background
- Large Arabic title: "مكتبة عكارون البحثية"
- Subtitle in English: "Akaroon — A Sudanese Heritage Digital Library"
- Fade in + hold 4s + fade out

**Audio:** Soft ambient music (no speech)

**Code hint:**
```jsx
<Fade duration={8} easing={Easing.inOut(Easing.cubic)}>
  <div style={{background: '#0B3D38', color: '#fff', textAlign: 'center'}}>
    <h1 style={{fontSize: 72, marginBottom: 20}}>مكتبة عكارون البحثية</h1>
    <p style={{fontSize: 28}}>Akaroon — A Sudanese Heritage Digital Library</p>
  </div>
</Fade>
```

---

### Scene 2: Homepage Tour (8-20s)
**Duration:** 12 seconds

**Visuals:**
- Fade into full homepage screenshot
- Zoom + pan: hero section with search input
- Voice-over: "Akaroon preserves over 2,000 Arabic academic documents..."
- Pan down to 7 category cards
- Voice-over: "...across seven thematic categories"

**Screenshot source:** `development.akaroon.com/`

**Voice-over (Arabic):**
"مكتبة عكارون تحافظ على أكثر من ألفي وثيقة أكاديمية باللغة العربية، عبر سبعة تصنيفات موضوعية."

**English (optional subtitle):**
"Akaroon preserves over 2,000 Arabic academic documents across 7 subject categories."

**Code hint:**
```jsx
<Sequence from={8} durationInFrames={12 * 30}>
  <Img src="/homepage.png" />
  <VoiceOver text="Voice-over in Arabic" startTime={0} />
</Sequence>
```

---

### Scene 3: Normal Search — عادي (20-35s)
**Duration:** 15 seconds

**Setup:** Show browser window with search bar in focus

**Actions:**
1. **0-3s:** Search bar is empty, ready for input
2. **3-8s:** Type "تعليم" (character by character animation)
3. **8-10s:** Hit "بحث" button (click animation)
4. **10-15s:** Results appear with loading spinner transitioning to cards

**Visuals:**
- Show 3 cards loading sequentially
- Each card: cover image → title → author/year/field
- Badge shows "التعليم" (education category)
- Counter: "جارٍ البحث (1/7)..." → final count

**Voice-over (Arabic):**
"الوضع الأول: البحث العادي. اكتب كلمة البحث وستظهر النتائج الدقيقة على الفور."

**English (subtitle):**
"Mode 1: Normal Search — Type your search term and get precise results instantly."

**Code hint:**
```jsx
<Sequence from={20 * 30} durationInFrames={15 * 30}>
  <TypeAnimation text="تعليم" duration={5} />
  <ButtonClick at={8} />
  <LoadingAnimation />
  <ResultCards count={3} />
</Sequence>
```

---

### Scene 4: Semantic Search — دلالي (35-50s)
**Duration:** 15 seconds

**Setup:** Results page with mode toggle visible

**Actions:**
1. **0-3s:** Current mode shows "🔤 عادي" highlighted
2. **3-6s:** Click on "🧠 دلالي" button
3. **6-9s:** Mode switches (gold highlight animation)
4. **9-12s:** Results reorganize with more matches
5. **12-15s:** Synonym hint appears: "بحث موسّع يشمل المترادفات: ..."

**Visuals:**
- Mode toggle in gold (#C9841A)
- Active button fills with gold, white text
- Results count increases (e.g., 12 → 42 results)
- Synonym tags appear below counter: "معلمي", "متعلم", "استعلام", etc.

**Voice-over (Arabic):**
"الوضع الثاني: البحث الدلالي. يوسّع البحث تلقائياً ليشمل المرادفات والجذور واللهجة السودانية."

**English (subtitle):**
"Mode 2: Semantic Search — Automatically expands to include synonyms, word roots, and Sudanese dialect."

**Code hint:**
```jsx
<Sequence from={35 * 30} durationInFrames={15 * 30}>
  <ModeToggle from="normal" to="semantic" duration={3} />
  <ResultsUpdate count={42} duration={3} />
  <SynonymTags tags={['معلمي', 'متعلم', 'استعلام']} />
</Sequence>
```

---

### Scene 5: Deep Search — عميق (50-65s)
**Duration:** 15 seconds

**Setup:** Results page, ready to show deep mode

**Actions:**
1. **0-3s:** Click "🔬 عميق" button
2. **3-6s:** Mode switches, cards remain but now show OCR snippets
3. **6-12s:** Zoom into one card to show snippet detail
4. **12-15s:** Keyword "تعليم" is highlighted in yellow in the snippet

**Visuals:**
- Deep mode button fills with gold
- Below each result card: golden section "🔬 من نص الوثيقة"
- Snippet text (small, RTL) shows 2-3 lines with keyword highlighted
- Cursor hovers → "النص الكامل" link highlights (teal hover state)

**Voice-over (Arabic):**
"الوضع الثالث: البحث العميق. يقرأ النص الكامل لكل وثيقة باستخدام الذكاء الاصطناعي ويبحث بداخلها."

**English (subtitle):**
"Mode 3: Deep Search — Reads the full text of every document using AI and searches within it for any mention."

**Code hint:**
```jsx
<Sequence from={50 * 30} durationInFrames={15 * 30}>
  <ModeToggle from="semantic" to="deep" duration={3} />
  <OCRSnippetReveal duration={6} />
  <KeywordHighlight keyword="تعليم" color="#FFE082" />
  <LinkHover text="النص الكامل" />
</Sequence>
```

---

### Scene 6: Skeleton Loading & Relevance Sort (65-90s)
**Duration:** 25 seconds

**Setup:** Fresh search, showing the magic of progressive loading

**Actions:**
1. **0-2s:** User types new search and hits "بحث"
2. **2-4s:** Page shows 7 shimmer skeleton sections (gray gradient wave animation)
3. **4-12s:** Sections fill in one-by-one as AJAX responses arrive (not in alphabetical order)
   - الفلسفة section fills first (fastest)
   - Then السياسة, المجتمع, etc.
4. **12-20s:** Counter updates: "جارٍ البحث (1/7)..." → "(3/7)..." → "(7/7)..."
5. **20-25s:** All sections re-sort by relevance
   - التعليم moves to top (most relevant)
   - الفلسفة moves to bottom (fewest matches)
   - Each section fades briefly during reorder

**Visuals:**
- Skeleton cards shimmer with warm gradient (#EDE8DF → #E4DDD2)
- Spinner and progress text update in real-time
- Final sorted order shows: التعليم (most) → التأصيل → المجتمع → منظمات → الدولة → السياسة → الفلسفة (least)
- Each section header shows category name + result count badge

**Voice-over (Arabic):**
"البحث المتقدم: سبعة طلبات متوازية تعمل في نفس الوقت. الأقسام تظهر فور انتهاء كل طلب، ثم تُرتّب حسب الملاءمة."

**English (subtitle):**
"Advanced Search: 7 parallel requests run simultaneously. Sections appear as each completes, then auto-sort by relevance."

**Code hint:**
```jsx
<Sequence from={65 * 30} durationInFrames={25 * 30}>
  <Search query="جديد" at={2} />
  <SkeletonSections count={7} shimmer={true} duration={2} />
  <ProgressiveLoad
    sections={['philo', 'pol', 'soc', 'tas', 'edu', 'state', 'org']}
    fillDuration={6}
  />
  <ProgressCounter from="0/7" to="7/7" duration={8} />
  <RelevanceSort
    order={['edu', 'tas', 'soc', 'org', 'state', 'pol', 'philo']}
    duration={5}
  />
</Sequence>
```

---

### Scene 7: OCR Viewer (90-110s)
**Duration:** 20 seconds

**Setup:** User is on search results page, hovering over an OCR snippet

**Actions:**
1. **0-3s:** Cursor hovers over snippet's "النص الكامل" link
2. **3-5s:** Link highlights (teal with border)
3. **5-7s:** Click animation
4. **7-10s:** Page transitions to OCR viewer (fade in)
5. **10-15s:** Show styled markdown reader:
   - Header: document title, author, year, category
   - Toolbar: A−/A+ font size buttons, print button, download button
6. **15-18s:** Scroll through the rendered markdown text (RTL Arabic)
7. **18-20s:** Click print button → print preview appears

**Visuals:**
- OCR viewer has clean white/cream background, teal accents
- Markdown renders with proper Arabic typography
- YAML metadata fields displayed as a human-readable header
- Markdown body with proper paragraph breaks, emphasis formatting
- Toolbar is sticky at top with gold (#C9841A) buttons
- Font size controls show A− and A+ icons
- Print preview shows clean PDF layout without toolbar

**Voice-over (Arabic):**
"اضغط على 'النص الكامل' لفتح محرر نصوص مصمم خصيصاً. يمكنك تعديل حجم الخط، طباعة النص، أو تحميل ملف MD الأصلي."

**English (subtitle):**
"Click 'Full Text' to open a beautifully styled markdown reader. Adjust font size, print, or download the original MD file."

**Code hint:**
```jsx
<Sequence from={90 * 30} durationInFrames={20 * 30}>
  <ResultCard />
  <LinkHover text="النص الكامل" />
  <Click at={5} />
  <PageTransition
    from="search-results"
    to="ocr-viewer"
    duration={2}
  />
  <OCRViewerScreen
    title="الوثيقة الأكاديمية"
    author="الكاتب"
    year="2018"
    markdown={fullText}
  />
  <ToolbarInteraction>
    <FontSizeControl />
    <PrintButton showPreview={true} at={18} />
  </ToolbarInteraction>
</Sequence>
```

---

### Scene 8: Outro (110-120s)
**Duration:** 10 seconds

**Visuals:**
- Fade to dark teal background
- Large text (English): "Explore Akaroon Today"
- Smaller text (Arabic): "استكشف عكارون الآن"
- Website URL centered: `development.akaroon.com`
- Fade out with music crescendo

**Voice-over (Arabic + English):**
"Visit Akaroon to search 2,000+ academic documents in Arabic. Three search modes. Powered by AI. By Ibrahim Ahmed Omer."

**Code hint:**
```jsx
<Sequence from={110 * 30} durationInFrames={10 * 30}>
  <Fade duration={10} easing={Easing.inOut(Easing.cubic)}>
    <div style={{background: '#0B3D38', color: '#fff', textAlign: 'center'}}>
      <h1>Explore Akaroon Today</h1>
      <h2>استكشف عكارون الآن</h2>
      <p style={{fontSize: 24, marginTop: 40}}>development.akaroon.com</p>
      <p style={{fontSize: 16, marginTop: 20, opacity: 0.7}}>By Ibrahim Ahmed Omer</p>
    </div>
  </Fade>
</Sequence>
```

---

## Remotion Setup Instructions

### 1. Create Remotion Project
```bash
npx create-video akaroon-demo --typescript
cd akaroon-demo
```

### 2. Install Dependencies
```bash
npm install @remotion/cli @remotion/react
```

### 3. Create Main Composition
**File:** `src/Composition.tsx`

```tsx
import { Composition } from 'Remotion';
import { AkaroonDemo } from './AkaroonDemo';

export const MyComposition = () => {
  return (
    <Composition
      id="AkaroonDemo"
      component={AkaroonDemo}
      durationInFrames={3600} // 120 seconds at 30fps
      fps={30}
      width={1920}
      height={1080}
    />
  );
};
```

### 4. Create Video Component
**File:** `src/AkaroonDemo.tsx`

Structure the component with 8 Sequence blocks (one per scene). Each scene imports or uses:
- `<Img src="..." />` for screenshots
- `<Video src="..." />` for screen recordings
- `<Fade>` for transitions
- `<Sequence>` for timing
- Custom components for animations (TypeAnimation, ButtonClick, etc.)

### 5. Key Assets Needed
```
src/assets/
├── homepage.png          ← screenshot of development.akaroon.com homepage
├── search-results.png    ← screenshot of search results page
├── deep-search.png       ← screenshot with OCR snippets visible
├── ocr-viewer.png        ← screenshot of markdown reader
├── skeleton-loading.mp4  ← 5s screen recording of skeleton load
├── relevance-sort.mp4    ← 10s screen recording of re-sorting
└── audio/
    ├── ambient-music.mp3  ← background music (120s)
    ├── voiceover-ar.mp3   ← Arabic voice-over (compiled)
    └── voiceover-en.mp3   ← English subtitles (optional)
```

### 6. Build & Render
```bash
npm run build

# Render as MP4 (1080p, H.264)
npx remotion render AkaroonDemo video.mp4 \
  --width 1920 \
  --height 1080 \
  --still-frame 0
```

---

## Recording Assets — How to Capture

### Screenshots
1. Open `development.akaroon.com` in browser at 1920×1080
2. Use Chrome DevTools → F12 → Cmd+Shift+P → "Screenshot"
3. Save as PNG: `homepage.png`, `search-results.png`, etc.

### Screen Recordings
1. Open `development.akaroon.com` at 1920×1080
2. Use **QuickTime Player** (macOS) or **OBS Studio** (cross-platform):
   - New Screen Recording
   - Record the specific action (skeleton loading, sorting, etc.)
   - Export as MP4, 30fps
3. Save as: `skeleton-loading.mp4`, `relevance-sort.mp4`

### Voice-Over
1. Use Audacity or Adobe Audition
2. Record Arabic and English separately
3. Mix with background music
4. Export as MP3, mono, 128kbps
5. Save as: `voiceover-ar.mp3`, `voiceover-en.mp3`

### Background Music
- License-free: Epidemic Sound, Artlist, or Free Music Archive
- Genre: Soft ambient, 120 seconds
- Format: MP3, royalty-free for commercial use

---

## Remotion Tips

### Animation Shortcuts
```tsx
// Type animation
<TypeAnimation text="تعليم" duration={5} delay={3} />

// Button click animation
<div style={{
  opacity: isClicked ? 0.8 : 1,
  boxShadow: isClicked ? '0 0 20px rgba(0,0,0,0.3)' : 'none',
}}>Click me</div>

// Fade in/out
<Fade duration={frames} easing={Easing.inOut(Easing.cubic)}>
  <YourComponent />
</Fade>

// Skeleton shimmer
<div style={{
  background: 'linear-gradient(90deg, #ede8df 25%, #e4ddd2 38%, #ede8df 63%)',
  backgroundSize: '200% 100%',
  animation: 'shimmer 1.6s ease-in-out infinite',
}} />
```

### Performance
- Use `<Sequence>` to delay rendering of later scenes (don't load all assets upfront)
- Optimize PNG/MP4 files (ImageOptim, ffmpeg)
- Render in steps: `--concurrency 4`

---

## Final Output

**Target specs:**
- Format: MP4 (H.264, AAC audio)
- Resolution: 1920×1080 (1080p)
- Frame rate: 30fps
- Duration: 120 seconds
- File size: ~150–200 MB (depends on compression)

**Platforms:**
- YouTube: Upload in 1080p, Remotion generates proper metadata
- LinkedIn: Same MP4, add captions via LinkedIn's tool
- Twitter: Trim to 15s teaser, link to full YouTube video

---

## Voice-Over Script (Full Text)

### Scene 2 (Homepage)
**Arabic:** مكتبة عكارون تحافظ على أكثر من ألفي وثيقة أكاديمية باللغة العربية، عبر سبعة تصنيفات موضوعية.
**English:** Akaroon preserves over 2,000 Arabic academic documents across 7 subject categories.

### Scene 3 (Normal Search)
**Arabic:** الوضع الأول: البحث العادي. اكتب كلمة البحث وستظهر النتائج الدقيقة على الفور.
**English:** Mode 1: Normal Search — Type your search term and get precise results instantly.

### Scene 4 (Semantic)
**Arabic:** الوضع الثاني: البحث الدلالي. يوسّع البحث تلقائياً ليشمل المرادفات والجذور واللهجة السودانية.
**English:** Mode 2: Semantic Search — Automatically expands to include synonyms, word roots, and Sudanese dialect.

### Scene 5 (Deep)
**Arabic:** الوضع الثالث: البحث العميق. يقرأ النص الكامل لكل وثيقة باستخدام الذكاء الاصطناعي ويبحث بداخلها.
**English:** Mode 3: Deep Search — Reads the full text of every document using AI and searches within it.

### Scene 6 (Loading)
**Arabic:** البحث المتقدم: سبعة طلبات متوازية تعمل في نفس الوقت. الأقسام تظهر فور انتهاء كل طلب، ثم تُرتّب حسب الملاءمة.
**English:** Advanced Search: 7 parallel requests run simultaneously. Sections appear as each completes, then auto-sort by relevance.

### Scene 7 (OCR)
**Arabic:** اضغط على 'النص الكامل' لفتح محرر نصوص مصمم خصيصاً. يمكنك تعديل حجم الخط، طباعة النص، أو تحميل ملف MD الأصلي.
**English:** Click 'Full Text' to open a beautifully styled markdown reader. Adjust font size, print, or download the original.

### Scene 8 (Outro)
**Arabic + English:** استكشف عكارون الآن — Explore Akaroon Today. Two thousand Arabic academic documents. Three search modes. Powered by AI. By Ibrahim Ahmed Omer.

---

## Additional Notes

- **Color Palette:** Use Akaroon brand colors throughout:
  - Dark Teal: `#0B3D38`
  - Gold: `#C9841A`
  - Cream: `#FDF6EC`
  - Text: `#2D1B00`

- **Typography:** Use Tajawal font (Google Fonts) for all Arabic text

- **RTL Layout:** Ensure all Arabic text renders right-to-left (`direction: rtl` in CSS)

- **Subtitles:** Embed bilingual subtitles (Arabic + English) in the video, or upload SRT file to YouTube

---

## Timeline Summary

| Time | Scene | Duration |
|------|-------|----------|
| 0–8s | Intro | 8s |
| 8–20s | Homepage | 12s |
| 20–35s | Normal Search | 15s |
| 35–50s | Semantic Search | 15s |
| 50–65s | Deep Search | 15s |
| 65–90s | Progressive Loading | 25s |
| 90–110s | OCR Viewer | 20s |
| 110–120s | Outro | 10s |

**Total: 120 seconds (2 minutes)**
