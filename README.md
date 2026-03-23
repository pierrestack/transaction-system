# 📦 Transaction Management API (Deposit, Withdrawal, Transfer)

## 📖 Description

This API provides a robust system for handling financial transactions, including:

* 💰 **Deposit**
* 💸 **Withdrawal**
* 🔁 **Transfer between accounts**

The architecture follows a **two-step transaction pattern** inspired by fintech systems:

* **INIT** → initialize a transaction and generate a token
* **EXECUTE** → securely process the transaction

---

## 🧠 Core Concepts

### 🔐 Transaction Token

Each transaction is initialized with a **unique token**, ensuring:

* Idempotency (prevents duplicate execution)
* Traceability
* Security

---

### 🔄 INIT / EXECUTE Pattern

| Step    | Description                                  |
| ------- | -------------------------------------------- |
| INIT    | Creates a transaction with `pending` status  |
| EXECUTE | Applies the transaction and updates balances |

---

### 📊 Ledger System (Operations)

Each transaction generates accounting entries:

* `credit` → adds funds
* `debit` → removes funds

---

## 🚀 Technologies

* Laravel (Backend API)
* Laravel Sanctum (Authentication)
* MySQL / PostgreSQL
* Filament (Admin Panel)
* Eloquent ORM
* Database Transactions (`DB::transaction`)

---

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   ├── Responses/
├── Models/
├── Services/
├── Factories/
```

---

## 🔌 API Endpoints

### 🔹 Deposit

**INIT**

```
POST /api/transactions/init-deposit
```

**EXECUTE**

```
POST /api/transactions/execute-deposit
```

---

### 🔹 Withdrawal

**INIT**

```
POST /api/transactions/init-withdrawal
```

**EXECUTE**

```
POST /api/transactions/execute-withdrawal
```

---

### 🔹 Transfer

**INIT**

```
POST /api/transactions/init-transfer
```

**EXECUTE**

```
POST /api/transactions/execute-transfer
```

---

## 📥 Example API Response

```json
{
  "status": "success",
  "message": "Transfer initialized",
  "data": {
    "token": "uuid...",
    "reference": "TRF-XXXXX"
  }
}
```

---

## ⚙️ Business Logic

### ✔️ INIT Phase

* Generates a unique token
* Creates a `pending` transaction
* Does not modify balances

---

### ✔️ EXECUTE Phase

* Validates token
* Ensures transaction is `pending`
* Checks account balance (for withdrawal/transfer)
* Uses `lockForUpdate()` to prevent race conditions
* Updates account balances
* Creates ledger entries (operations)
* Marks transaction as `completed`

---

## 🔒 Security

* Database transactions (`DB::transaction`)
* Pessimistic locking (`lockForUpdate`)
* Request validation via FormRequest
* Idempotency via token
* Transaction status verification

---

## 🧪 Validation

Each endpoint uses dedicated **Form Requests** to validate:

* Number accounts
* Amount values
* Business rules (e.g., sufficient balance)

---

## 🧱 Architecture

* **Service Layer** → handles business logic (TransactionService)
* **Factory Pattern** → creates operations (ledger entries)
* **Response Classes** → standardizes API responses
* Clean separation of concerns

---

## 🖥️ Admin Panel

Built with Filament, providing:

* Account management
* Transaction monitoring
* Multi-tab transaction forms (Deposit / Withdrawal / Transfer)

---

## 🧑‍💻 Installation

```
git clone https://github.com/pierrestack/transaction-system.git
cd transaction-system

composer install
npm install && npm run build
cp .env.example .env

php artisan key:generate
php artisan migrate

php artisan serve
```

---

## 📌 Best Practices Applied

* Clean Architecture
* SOLID principles
* DRY (Don't Repeat Yourself)
* RESTful API standards
* INIT / EXECUTE transaction pattern

---

## 📞 Contact

This project was developed as part of an advanced exploration of API architecture and financial transaction systems.
