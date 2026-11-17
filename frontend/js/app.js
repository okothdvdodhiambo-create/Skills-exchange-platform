async function apiRequest(url, options = {}) {
  const defaultHeaders = {'Content-Type': 'application/json'};
  const config = {
    ...options,
    headers: {
      ...defaultHeaders,
      ...(options.headers || {}),
    },
  };

  const response = await fetch(url, config);
  const text = await response.text();
  let data;

  try {
    data = text ? JSON.parse(text) : {};
  } catch (error) {
    throw new Error('Invalid JSON response.');
  }

  if (!response.ok || (data && data.success === false)) {
    const message = data && data.message ? data.message : 'Request failed.';
    throw new Error(message);
  }

  return data.data !== undefined ? data.data : data;
}

function showMessage(type, text) {
  const box = document.getElementById('messageBox');
  if (!box) return;

  box.className = `alert alert-${type} alert-box show`;
  box.textContent = text;
}

function setLoading(button, isLoading) {
  if (!button) return;
  button.disabled = isLoading;
  button.textContent = isLoading ? 'Processing...' : button.dataset.originalText || button.textContent;
}

function attachFormSubmitBehavior(formId, onSubmit) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.dataset.originalText = submitButton.textContent;
      setLoading(submitButton, true);
    }

    try {
      await onSubmit(new FormData(form));
    } catch (error) {
      showMessage('danger', error.message);
    } finally {
      if (submitButton) setLoading(submitButton, false);
    }
  });
}

function buildApiUrl(path) {
  const base = '/community-skill-exchange/gateway/index.php';
  return `${base}${path}`;
}

function attachLogout() {
  const button = document.getElementById('logoutButton');
  if (!button) return;

  button.addEventListener('click', async () => {
    try {
      await apiRequest(buildApiUrl('/api/users/logout'), {method: 'POST'});
      window.location.href = '/community-skill-exchange/login.php';
    } catch (error) {
      showMessage('danger', error.message);
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  attachLogout();
});
