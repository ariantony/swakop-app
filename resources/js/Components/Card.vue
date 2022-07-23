<template>
  <div class="flex flex-col border-2 border-slate-400 rounded-md print:hidden">
    <div class="p-1 bg-slate-100 rounded-t-md">
      <slot name="header" />
    </div>

    <div class="p-4 bg-slate-50 border-y rounded-md">
      <slot name="body" />
    </div>

    <div class="bg-slate-100 rounded-b-md">
      <slot name="footer" />
    </div>
  </div>

  <div class="hidden print:flex flex-col">
    <div class="p-4">
      <slot name="print" />
    </div>
  </div>
</template>