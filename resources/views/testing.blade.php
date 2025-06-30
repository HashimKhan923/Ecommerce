<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Segment Query Builder</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 2rem;
      background: #f9f9f9;
    }
    .segment-builder {
      background: white;
      border-radius: 8px;
      padding: 1.5rem;
      max-width: 800px;
      margin: auto;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .query-line {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }
    select, input[type="text"] {
      padding: 0.5rem;
      margin-right: 0.5rem;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .query-preview {
      background: #f4f4f4;
      border-radius: 4px;
      padding: 1rem;
      font-family: monospace;
      color: #333;
    }
    .btn {
      padding: 0.5rem 1rem;
      background: #4f46e5;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn:hover {
      background: #4338ca;
    }
  </style>
</head>
<body>
  <div class="segment-builder">
    <h2>Create Customer Segment</h2>

    <div id="query-lines"></div>

    <button class="btn" onclick="addLine()">+ Add Condition</button>

    <h3 style="margin-top: 2rem;">Query Preview:</h3>
    <div class="query-preview" id="preview">WHERE ...</div>
  </div>

<script>
  let index = 0;
  const operators = ['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE'];

  const fields = [
    { label: "Customer Name", value: "users.name" },
    { label: "Customer Email", value: "users.email" },
    { label: "Country", value: "users.country" },
    { label: "State", value: "users.state" },
    { label: "City", value: "users.city" },
    { label: "Phone", value: "users.phone" },
    { label: "Email Verified", value: "users.email_verified_at" },
    { label: "Is Active", value: "users.is_active" },
    { label: "Sale", value: "my_customers.sale" },
    { label: "Total Orders", value: "orders.count" },
    { label: "Order Amount", value: "orders.amount" },
    { label: "Order Created At", value: "orders.created_at" },
    { label: "Payment Status", value: "orders.payment_status" },
    { label: "Delivery Status", value: "orders.delivery_status" }
  ];

  function addLine() {
    const container = document.getElementById('query-lines');
    const div = document.createElement('div');
    div.classList.add('query-line');
    div.setAttribute('data-index', index);

    div.innerHTML = `
      <select onchange="updatePreview()" class="field">
        ${fields.map(f => `<option value="${f.value}">${f.label}</option>`).join('')}
      </select>
      <select onchange="updatePreview()" class="operator">
        ${operators.map(op => `<option value="${op}">${op}</option>`).join('')}
      </select>
      <input type="text" oninput="updatePreview()" class="value" placeholder="Value" />
      <button onclick="this.parentElement.remove(); updatePreview();">❌</button>
    `;
    container.appendChild(div);
    index++;
  }

  function updatePreview() {
    const lines = document.querySelectorAll('.query-line');
    const conditions = [];

    lines.forEach(line => {
      const field = line.querySelector('.field').value;
      const operator = line.querySelector('.operator').value;
      const value = line.querySelector('.value').value;

      if (field && operator && value !== '') {
        let valStr = value;
        if (isNaN(value) && operator.indexOf('LIKE') === -1) {
          valStr = `'${value}'`;
        }
        conditions.push(`${field} ${operator} ${valStr}`);
      }
    });

    const preview = document.getElementById('preview');
    preview.textContent = conditions.length ? 'WHERE ' + conditions.join(' AND ') : 'WHERE ...';
  }

  addLine();
</script>
</body>
</html>