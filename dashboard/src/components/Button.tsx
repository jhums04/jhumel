import type { ComponentProps } from 'react'

type ButtonProps = Omit<ComponentProps<'button'>, 'type'> & {
  type?: 'button' | 'submit'
  variant?: 'primary' | 'secondary'
}

const variantClassNames = {
  primary: 'bg-navy text-white hover:bg-amber hover:text-black',
  secondary: 'border border-black text-black hover:border-navy hover:bg-amber-50 hover:text-navy',
}

export function Button({ type = 'button', variant = 'primary', className = '', ...buttonProps }: ButtonProps) {
  return (
    <button
      type={type === 'submit' ? 'submit' : 'button'}
      className={`inline-flex items-center justify-center rounded-full px-5 py-3 font-semibold transition-colors motion-reduce:transition-none disabled:cursor-not-allowed disabled:opacity-60 ${variantClassNames[variant]} ${className}`}
      {...buttonProps}
    />
  )
}
