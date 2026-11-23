import { Route, Routes } from 'react-router'
import { AppLayout } from './components/AppLayout'
import { LoginPage } from './features/auth/LoginPage'
import { RequireAuth } from './features/auth/RequireAuth'
import { LeadDrawer } from './features/leads/LeadDrawer'
import { PipelinePage } from './features/pipeline/PipelinePage'

export function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route element={<RequireAuth />}>
        <Route element={<AppLayout />}>
          <Route path="/" element={<PipelinePage />}>
            <Route path="leads/:leadId" element={<LeadDrawer />} />
          </Route>
        </Route>
      </Route>
    </Routes>
  )
}
