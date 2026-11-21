<section id="contact" class="section contact">
    <div class="container contact__grid">
        <div class="contact__info reveal">
            <p class="eyebrow">Get in Touch</p>
            <h2 class="section__title">Let's work together.</h2>
            <p class="section__lead">Have a project in mind or just want to say hello? Feel free to reach out.</p>

            <ul class="contact__list">
                @if ($profile->email)
                    <li><x-icon name="mail" /> <a href="mailto:{{ $profile->email }}">{{ $profile->email }}</a></li>
                @endif
                @if ($profile->github_url)
                    <li><x-icon name="github" /> <a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer">GitHub</a></li>
                @endif
                @if ($profile->instagram_url)
                    <li><x-icon name="instagram" /> <a href="{{ $profile->instagram_url }}" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                @endif
            </ul>
        </div>

        <form method="POST" action="{{ route('contact.store') }}" class="form contact__form reveal">
            @csrf

            @if (session('success'))
                <x-alert>{{ session('success') }}</x-alert>
            @endif
            @if ($errors->any())
                <x-alert type="error">Please fix the highlighted fields and try again.</x-alert>
            @endif

            {{-- Spam protection: honeypot + minimum fill time + throttle:5,1 on the route --}}
            <div class="hp" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="started_at" value="{{ encrypt(now()->timestamp) }}">

            <div class="form-row">
                <x-field name="name" label="Name" required maxlength="80" autocomplete="name" />
                <x-field name="email" type="email" label="Email" required maxlength="255" autocomplete="email" />
            </div>
            <x-field name="subject" label="Subject" required maxlength="150" />
            <x-field name="message" type="textarea" label="Message" :rows="6" required minlength="10" maxlength="5000" />

            <button type="submit" class="btn btn--dark">Send Message <x-icon name="arrow-right" /></button>
        </form>
    </div>
</section>
