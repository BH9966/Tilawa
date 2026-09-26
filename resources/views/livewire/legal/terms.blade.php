<div class="min-h-screen">
    <header class="border-b border-tilawa-line bg-tilawa-surface px-6 py-4 md:px-12">
        <div class="mx-auto flex max-w-3xl items-center gap-4">
            <a href="{{ url()->previous() === url()->current() ? route('landing') : url()->previous() }}"
                class="flex h-9 w-9 items-center justify-center rounded-full text-tilawa-sub transition hover:bg-tilawa-line hover:text-tilawa-ink">
                <x-ui.icon name="arrow-left" class="h-5 w-5" />
            </a>
            <h1 class="text-lg font-bold text-tilawa-ink">Terms of Service</h1>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-6 py-10 md:px-12">
        <p class="mb-8 text-sm text-tilawa-sub">Last updated {{ now()->format('F Y') }}</p>

        <div class="space-y-8 text-[15px] leading-relaxed text-tilawa-ink">
            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">1. About Tilawa</h2>
                <p class="text-tilawa-sub">
                    Tilawa is a free reading companion for the Qur'an, built and operated by Ubitech Solutions
                    Limited ("Ubitech", "we", "us"). By creating an account or using the app, you agree to these
                    terms.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">2. Your account</h2>
                <p class="text-tilawa-sub">
                    You're responsible for keeping your login credentials secure and for the activity on your
                    account. Let us know if you believe your account has been accessed without your permission.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">3. Qur'anic content</h2>
                <p class="text-tilawa-sub">
                    Arabic text, transliteration and translation are provided through the
                    <a href="https://alquran.cloud" target="_blank" rel="noopener" class="font-semibold text-tilawa-teal-dark underline underline-offset-2">Al Quran Cloud API</a>.
                    We take reasonable care to present this content accurately, but for matters of religious
                    ruling or precise recitation, please refer to a qualified scholar or a printed Mushaf.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">4. Acceptable use</h2>
                <p class="text-tilawa-sub">
                    Please use Tilawa respectfully — don't attempt to disrupt the service, access other users'
                    accounts or data, or use the app for any unlawful purpose.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">5. Availability</h2>
                <p class="text-tilawa-sub">
                    Tilawa is provided free of charge, "as is." We aim to keep it available and accurate, but we
                    don't guarantee uninterrupted access, and features may change as the app improves.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">6. Changes to these terms</h2>
                <p class="text-tilawa-sub">
                    We may update these terms from time to time. Continued use of Tilawa after a change means you
                    accept the updated terms.
                </p>
            </section>

            <section>
                <h2 class="mb-2 text-base font-bold text-tilawa-ink">7. Contact</h2>
                <p class="text-tilawa-sub">
                    Questions about these terms? Reach us at
                    <a href="mailto:{{ config('mail.from.address') }}" class="font-semibold text-tilawa-teal-dark underline underline-offset-2">{{ config('mail.from.address') }}</a>.
                </p>
            </section>
        </div>
    </main>
</div>
