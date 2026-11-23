import { useEffect, useRef, type ReactNode } from 'react'

type DrawerProps = {
  title: string
  subtitle?: string | null
  onClose: () => void
  children: ReactNode
}

const HEADING_ID = 'drawer-heading'

// A side panel built on the native <dialog>: showModal() traps focus, closes on Escape, makes the page behind it
// unreachable, and returns focus to the element that opened it. Every way of closing goes through dialog.close(),
// which is what triggers that focus return; onClose then lets the parent remove the drawer.
export function Drawer({ title, subtitle, onClose, children }: DrawerProps) {
  const dialogRef = useRef<HTMLDialogElement>(null)

  useEffect(() => {
    const dialog = dialogRef.current
    if (dialog !== null && !dialog.open) {
      dialog.showModal()
    }
  }, [])

  function closeDialog() {
    dialogRef.current?.close()
  }

  return (
    <dialog
      ref={dialogRef}
      aria-labelledby={HEADING_ID}
      onClose={onClose}
      // A click on the dimmed backdrop lands on the dialog itself; clicks inside land on the panel.
      onClick={(event) => {
        if (event.target === event.currentTarget) {
          closeDialog()
        }
      }}
      className="fixed inset-y-0 right-0 left-auto m-0 h-full max-h-none w-full max-w-xl bg-white p-0 text-black backdrop:bg-black/40"
    >
      <div className="h-full overflow-y-auto">
        <div className="flex items-start justify-between gap-4 px-6 py-5">
          <div className="min-w-0">
            <h2 id={HEADING_ID} className="text-xl font-bold break-words">
              {title}
            </h2>
            {subtitle !== undefined && subtitle !== null && <p className="text-gray-600">{subtitle}</p>}
          </div>
          <button
            type="button"
            onClick={closeDialog}
            aria-label="Close"
            autoFocus
            className="inline-flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded text-3xl leading-none text-gray-600 hover:text-black"
          >
            &times;
          </button>
        </div>
        {children}
      </div>
    </dialog>
  )
}
