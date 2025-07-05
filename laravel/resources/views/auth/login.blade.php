<x-auth.layout title="Login | LuxuryFinds">
  <div class="container">
    <!-- Left: Form Section -->
    <div class="form-section">
      <div class="form-box">
        <div class="text-center">
          <h2>Welcome back To Luxury<span>Finds</span></h2>
          <p class="subtext">Log in to your account</p>
        </div>
        <p class="unexpected_error"  style="display:{{$errors->any()?'block':'none'}};"><x-errors.error /> </p>
        <!-- Google Sign Up -->
        <a href="{{ route('google.login') }}" class="Continue-with-google ">
           <svg width="20px" height="20px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="#4285F4" d="M14.9 8.161c0-.476-.039-.954-.121-1.422h-6.64v2.695h3.802a3.24 3.24 0 01-1.407 2.127v1.75h2.269c1.332-1.22 2.097-3.02 2.097-5.15z"></path><path fill="#34A853" d="M8.14 15c1.898 0 3.499-.62 4.665-1.69l-2.268-1.749c-.631.427-1.446.669-2.395.669-1.836 0-3.393-1.232-3.952-2.888H1.85v1.803A7.044 7.044 0 008.14 15z"></path><path fill="#FBBC04" d="M4.187 9.342a4.17 4.17 0 010-2.68V4.859H1.849a6.97 6.97 0 000 6.286l2.338-1.803z"></path><path fill="#EA4335" d="M8.14 3.77a3.837 3.837 0 012.7 1.05l2.01-1.999a6.786 6.786 0 00-4.71-1.82 7.042 7.042 0 00-6.29 3.858L4.186 6.66c.556-1.658 2.116-2.89 3.952-2.89z"></path></g></svg>
           <p>Continue with Google</p>
        </a>

        <!-- Divider -->
        <div class="divider"><span>or log in with email</span></div>

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="form">
          @csrf
          <div>
            <input id="email" name="email" type="email"  placeholder="" required/>
            <label for="email">Email address</label>
            <span class="error-message email" id="email-error"></span>
          </div>
          <div>
            <input type="password" name="password" class="input" placeholder="" required>
            <label for="password">Password</label>
            <span class="error-message password" id="password-error"></span>
          </div>

          <button>
            Log In
            <span class="loading"></span>
          </button>
        </form>

        <p class="bottom-text">
          Don't have an account?
          <a href="{{ route('register') }}">Register</a>
        </p>
      </div>
    </div>

    <!-- Right: Image Section -->
    <div class="image-section">
      <img
        src="{{ asset('assets/img/freestocks-_3Q3tsJ01nc-unsplash.jpg') }}"
        alt="Luxury Shopping"
      />
    </div>

  </div>
</x-auth.layout>