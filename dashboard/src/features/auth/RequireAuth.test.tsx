import { render, screen } from '@testing-library/react'
import { MemoryRouter, Route, Routes, useLocation } from 'react-router'
import { describe, expect, it, vi } from 'vitest'
import { createTestSession } from '../../test/createTestSession'
import { RequireAuth } from './RequireAuth'
import { useSession, type SessionState } from './useSession'

vi.mock('./useSession', () => ({ useSession: vi.fn<() => SessionState>() }))

function LoginPageStandIn() {
  const location = useLocation()
  return <p>Login page, returning to {JSON.stringify(location.state)}</p>
}

function renderProtectedRoute(sessionState: SessionState) {
  vi.mocked(useSession).mockReturnValue(sessionState)

  render(
    <MemoryRouter initialEntries={['/leads?stage=new']}>
      <Routes>
        <Route path="/login" element={<LoginPageStandIn />} />
        <Route element={<RequireAuth />}>
          <Route path="/leads" element={<p>Protected page</p>} />
        </Route>
      </Routes>
    </MemoryRouter>,
  )
}

describe('RequireAuth', () => {
  it('shows a loading message while the session is being read', () => {
    renderProtectedRoute({ status: 'loading' })

    expect(screen.getByRole('status')).toHaveTextContent('Loading')
    expect(screen.queryByText('Protected page')).not.toBeInTheDocument()
  })

  it('sends a signed-out visitor to the login page and remembers where they were going', () => {
    renderProtectedRoute({ status: 'signed-out' })

    expect(screen.getByText('Login page, returning to {"from":"/leads?stage=new"}')).toBeInTheDocument()
    expect(screen.queryByText('Protected page')).not.toBeInTheDocument()
  })

  it('shows the page to a signed-in user', () => {
    renderProtectedRoute({ status: 'signed-in', session: createTestSession('demo@jhumel.dev') })

    expect(screen.getByText('Protected page')).toBeInTheDocument()
  })
})
