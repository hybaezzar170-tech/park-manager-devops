import { render, screen } from '@testing-library/react';
import App from './App';

test('affiche le formulaire de connexion Park Manager', () => {
  render(<App />);

  expect(screen.getByText('Email')).toBeInTheDocument();
  expect(screen.getByText('Mot de passe')).toBeInTheDocument();
  expect(screen.getByRole('button', { name: /se connecter/i })).toBeInTheDocument();
});
