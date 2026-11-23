import { Route, Routes } from 'react-router'
import { AppLayout } from './components/AppLayout'
import { LoginPage } from './features/auth/LoginPage'
import { RequireAuth } from './features/auth/RequireAuth'
import { PipelinePage } from './features/pipeline/PipelinePage'

export function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route element={<RequireAuth />}>
        <Route element={<AppLayout />}>
          <Route index element={<PipelinePage />} />
        </Route>
      </Route>
    </Routes>
  )
}
