import { createRoot } from 'react-dom/client';
import App from './App';
import './styles/converter.css';

document.querySelectorAll<HTMLElement>('.cc-nxcode-root').forEach((container) => {
  const defaultFrom = container.dataset.defaultFrom || 'USD';
  const defaultTo = container.dataset.defaultTo || 'EUR';

  createRoot(container).render(
    <App defaultFrom={defaultFrom} defaultTo={defaultTo} />
  );
});
