<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabby Checkout Form</title>
</head>
<body>
    <h1>Tabby Checkout Form</h1>
    <form action="{{ route('tabby.create-session') }}" method="POST">
        @csrf
        <label for="name">Name:</label>
        <input type="text" id="name" name="buyer[name]" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="buyer[email]" required>
        <br><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="buyer[phone]" required>
        <br><br>

        <label for="address">Shipping Address:</label>
        <textarea id="address" name="shipping_address[address_line1]" required></textarea>
        <br><br>

        <label for="city">City:</label>
        <input type="text" id="city" name="shipping_address[city]" required>
        <br><br>

        <label for="country">Country:</label>
        <input type="text" id="country" name="shipping_address[country]" required>
        <br><br>

        <label for="zip">ZIP Code:</label>
        <input type="text" id="zip" name="shipping_address[zip]" required>
        <br><br>

        <label for="amount">Amount:</label>
        <input type="number" step="0.01" id="amount" name="payment[amount]" required>
        <br><br>

        <label for="currency">Currency:</label>
        <select id="currency" name="payment[currency]" required>
            <option value="AED">AED</option>
        </select>
        <br><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="payment[description]" required>
        <br><br>

        <label for="lang">Language:</label>
        <select id="lang" name="lang" required>
            <option value="en">English</option>
            <option value="ar">Arabic</option>
        </select>
        <br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
