<form action="{{ route('password.update') }}" method="POST" class="reset-password-form">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="password">New Password:</label>
        <input type="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" name="password_confirmation" required>
    </div>

    <button type="submit" class="submit-button">Reset Password</button>
</form>

<style>
/* Body Styling to Center the Form */
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f0f0f0; /* Optional: You can adjust the background color */
}

/* Form Styling */
.reset-password-form {
    max-width: 400px;
    width: 100%;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Form Group Styling */
.form-group {
    margin-bottom: 15px;
}

/* Label Styling */
.form-group label {
    display: block;
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
}

/* Input Field Styling */
.form-group input {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Focused Input Field Styling */
.form-group input:focus {
    border-color: #4a90e2;
    outline: none;
}

/* Submit Button Styling */
.submit-button {
    width: 100%;
    padding: 12px;
    background-color: #4a90e2;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

/* Submit Button Hover Effect */
.submit-button:hover {
    background-color: #357ab7;
}

/* Button Disabled State */
.submit-button:disabled {
    background-color: #d1d1d1;
    cursor: not-allowed;
}
</style>