<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Segment Query Builder with AND/OR</title>
  <style>
    body { font-family: sans-serif; padding: 2rem; background: #f9f9f9; }
    .segment-builder { background: white; border-radius: 8px; padding: 1.5rem; max-width: 800px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .query-line { display: flex; align-items: center; margin-bottom: 1rem; }
    select, input[type="text"], input[type="datetime-local"] { padding: 0.5rem; margin-right: 0.5rem; border-radius: 4px; border: 1px solid #ccc; }
    .query-preview { background: #f4f4f4; border-radius: 4px; padding: 1rem; font-family: monospace; color: #333; }
    .btn { padding: 0.5rem 1rem; background: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .btn:hover { background: #4338ca; }
  </style>
</head>
<body>
  <div class="segment-builder">
    <h2>Create Customer Segment</h2>

    <div id="query-lines"></div>

    <button class="btn" onclick="addLine()">+ Add Condition</button>

    <h3 style="margin-top: 2rem;">Query Preview:</h3>
    <div class="query-preview" id="preview">FROM customers WHERE ...</div>
  </div>

<script>
  let index = 0;

  const fields = [
    { label: "Customer Name", value: "customer.name" },
    { label: "Customer Email", value: "customer.email" },
    { label: "Email Verified", value: "customer.email_verified_at", type: "datetime" },
    { label: "Customer Status", value: "customer.is_active", type: "status", options: ["Active", "Not Active"] },
    { label: "Country", value: "customer.country" },
    { label: "State", value: "customer.state" },
    { label: "City", value: "customer.city" },
    { label: "Postal Code", value: "customer.postal_code" },
    { label: "Phone", value: "customer.phone" },
    { label: "Created At", value: "customer.created_at", type: "datetime" },
    { label: "Average Rating", value: "customer.average_rating" },
    { label: "Sale", value: "sale" },
    { label: "First Seen", value: "created_at", type: "datetime" },
    { label: "Last Updated", value: "updated_at", type: "datetime" },
    { label: "Total Orders", value: "orders.count" },
    { label: "Total Spent", value: "orders.amount" },
    { label: "Average Order Value", value: "orders.avg_amount" },
    { label: "Order Created At", value: "orders.created_at", type: "datetime" },
    { label: "Delivery Status", value: "orders.delivery_status", type: "status", options: ["Pending","Delivered"] },
    { label: "Shipping Amount", value: "orders.shipping_amount" },
    { label: "Tax", value: "orders.tax" },
  ];

  const operators = [
    { label: "Equal to", value: "=" },
    { label: "Not equal to", value: "!=" },
    { label: "Greater than", value: ">" },
    { label: "Less than", value: "<" },
    { label: "Greater than or equal to", value: ">=" },
    { label: "Less than or equal to", value: "<=" },
    { label: "LIKE", value: "LIKE" },
    { label: "NOT LIKE", value: "NOT LIKE" }
  ];

  function addLine() {
    const container = document.getElementById('query-lines');
    const div = document.createElement('div');
    div.classList.add('query-line');
    div.setAttribute('data-index', index);

    div.innerHTML = `
      ${index > 0 ? `<select class="joiner" onchange="updatePreview()">
        <option value="AND">AND</option>
        <option value="OR">OR</option>
      </select>` : ''}
      <select onchange="updateField(this)" class="field">
        ${fields.map(f => `<option value="${f.value}">${f.label}</option>`).join('')}
      </select>
      <select class="operator"></select>
      <span class="value-container">
        <input type="text" oninput="updatePreview()" class="value" placeholder="Value" />
      </span>
      <button onclick="this.parentElement.remove(); updatePreview();">❌</button>
    `;
    container.appendChild(div);

    updateField(div.querySelector('.field'));
    index++;
  }

  function updateField(fieldSelect) {
    const line = fieldSelect.closest('.query-line');
    const operatorSelect = line.querySelector('.operator');
    const valueContainer = line.querySelector('.value-container');

    const selectedField = fields.find(f => f.value === fieldSelect.value);

    if (selectedField.type === 'status') {
      operatorSelect.innerHTML = `
        <option value="=">Equal to</option>
        <option value="!=">Not equal to</option>
      `;
      valueContainer.innerHTML = `
        <select class="value" onchange="updatePreview()">
          ${selectedField.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
        </select>
      `;
    } else {
      operatorSelect.innerHTML = operators.map(op => `<option value="${op.value}">${op.label}</option>`).join('');

      if (selectedField.type === 'datetime') {
        valueContainer.innerHTML = `<input type="datetime-local" class="value" onchange="updatePreview()" />`;
      } else {
        valueContainer.innerHTML = `<input type="text" oninput="updatePreview()" class="value" placeholder="Value" />`;
      }
    }

    updatePreview();
  }

  function updatePreview() {
    const lines = document.querySelectorAll('.query-line');
    const conditions = [];

    lines.forEach((line, idx) => {
      const field = line.querySelector('.field').value;
      const operator = line.querySelector('.operator').value;
      const valueElem = line.querySelector('.value');
      const value = valueElem.value;
      const joiner = line.querySelector('.joiner') ? line.querySelector('.joiner').value : '';

      if (field && operator && value !== '') {
        let valStr = value;
        if (isNaN(value) && !valueElem.tagName.includes('SELECT') && operator.indexOf('LIKE') === -1) {
          valStr = `'${value}'`;
        }
        const condition = `${field} ${operator} ${valStr}`;
        if (idx === 0) {
          conditions.push(condition);
        } else {
          conditions.push(`${joiner} ${condition}`);
        }
      }
    });

    const preview = document.getElementById('preview');
    preview.textContent = conditions.length ? 'FROM customers WHERE ' + conditions.join(' ') : 'FROM customers WHERE ...';
  }

  addLine();
</script>
</body>
</html>
