<div class="min-h-screen">
    <header class="border-b border-tilawa-line bg-tilawa-surface px-6 py-4 md:px-12">
        <div class="mx-auto flex max-w-3xl items-center gap-4">
            <a href="{{ url()->previous() === url()->current() ? route('landing') : url()->previous() }}"
                class="flex h-9 w-9 items-center justify-center rounded-full text-tilawa-sub transition hover:bg-tilawa-line hover:text-tilawa-ink">
                <x-ui.icon name="arrow-left" class="h-5 w-5" />
            </a>
            <h1 class="text-lg font-bold text-tilawa-ink">Privacy Policy</h1>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-6 py-10 md:px-12">
        <p class="mb-8 text-sm text-tilawa-sub">Last updated {{ now()->format('F Y') }}</p>

        <div class="space-y-8 text-[15px] leading-relaxed text-tilawa-ink">
            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">1. What we collect</h2>
                <p class="text-tilawa-sub">
                    To create and secure your account, we store your name, email address and a securely hashed
                    password (we never store your password in plain text). To power features like bookmarks and
                    "Continue reading," we store the ayahs you save and the last surah/ayah you read.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">2. What we don't do</h2>
                <p class="text-tilawa-sub">
                    We don't sell your data, run third-party advertising, or share your information with anyone
                    outside Ubitech Solutions Limited, except where required by law.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">3. Qur'anic text source</h2>
                <p class="text-tilawa-sub">
                    The Qur'anic text, transliteration and translation displayed in Tilawa are fetched from the
                    <a href="https://alquran.cloud" target="_blank" rel="noopener" class="font-semibold text-tilawa-teal-dark underline underline-offset-2">Al Quran Cloud API</a>,
                    a third-party service. No personal data is sent to them — only the surah or ayah reference
                    needed to retrieve the text.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">4. Your control over your data</h2>
                <p class="text-tilawa-sub">
                    You can update your name and email, and change your password, at any time from your Profile
                    page. Deleting your account permanently removes your profile and all of your bookmarks.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">5. Security</h2>
                <p class="text-tilawa-sub">
                    We use industry-standard measures — including password hashing and encrypted sessions — to
                    protect your account. No online service can guarantee absolute security, but we take
                    reasonable steps to keep your data safe.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">6. Contact</h2>
                <p class="text-tilawa-sub">
                    Questions about your data? Reach us at
                    <a href="mailto:{{ config('mail.from.address') }}" class="font-semibold text-tilawa-teal-dark underline underline-offset-2">{{ config('mail.from.address') }}</a>.
                </p>
            </section>
        </div>
    </main>
</div>
