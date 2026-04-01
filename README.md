# 📦 Transaction Management API (Deposit, Withdrawal, Transfer)

![Laravel](https://img.shields.io/badge/Laravel-API-red)
![Docker](https://img.shields.io/badge/Docker-ready-blue)
![Octane](https://img.shields.io/badge/Octane-high--performance-green)

---

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
* Laravel Octane (High performance)
* FrankenPHP (Application server)
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

* Account numbers
* Amount values
* Business rules (e.g., sufficient balance)

---

## 🧱 Architecture

* **Service Layer** → handles business logic (`TransactionService`)
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

## 🧑‍💻 Installation (Without Docker)

```bash
git clone https://github.com/pierrestack/transaction-system.git
cd transaction-system

composer install
npm install && npm run build

cp .env.example .env

php artisan key:generate
php artisan migrate
php artisan db:seed

php artisan serve
```

Application available at:
👉 http://127.0.0.1:8000

---

## 🐳 Installation (With Docker - Recommended)

This project runs with **Laravel Octane + FrankenPHP** for high performance.

---

### 📦 Prerequisites

* Docker
* Docker Compose

---

### ⚙️ Setup

```bash
git clone https://github.com/pierrestack/transaction-system.git
cd transaction-system

cp .env.example .env
```

---

### 🔧 Environment Configuration

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=transaction_system_db
DB_USERNAME=admin
DB_PASSWORD=Password@2026!
```

---

### 🚀 Start Containers

```bash
docker-compose down -v
docker-compose up --build
```

---

### 🗄️ Run Migrations

```bash
docker exec -it transaction_system php artisan migrate
docker exec -it transaction_system php artisan db:seed
```

---

### ✅ Verify Octane

```bash
docker exec -it transaction_system php artisan octane:status
```

Expected output:

```
Octane server is running
```

---

## 🌐 Services

| Service    | URL                   |
| ---------- | --------------------- |
| API        | http://localhost:8000 |
| phpMyAdmin | http://localhost:8081 |

---

## ⚠️ Important Notes

* Do **not** use `php artisan serve` with Octane
* Always use `DB_HOST=db` in Docker
* Run `docker-compose down -v` to reset the database
* Ensure frontend assets are built (`npm run build`)

---

## 🛠️ Useful Commands

```bash
# Access container
docker exec -it transaction_system bash

# View logs
docker logs transaction_system

# Restart services
docker-compose restart
```

---

## 📌 Best Practices Applied

* Clean Architecture
* SOLID principles
* DRY (Don't Repeat Yourself)
* RESTful API design
* INIT / EXECUTE transaction pattern

---

## 📞 Contact

This project was developed as part of an advanced exploration of **API architecture and financial transaction systems**.
