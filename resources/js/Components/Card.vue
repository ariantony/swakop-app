<template>
  <div class="flex flex-col border rounded-md">
    <div class="bg-slate-100 rounded-t-md">
      <slot name="header" />
    </div>

    <div class="p-4 bg-slate-50 border-y rounded-md">
      <slot name="body" />
    </div>

    <div class="bg-slate-100 rounded-b-md">
      <slot name="footer" />
    </div>
  </div>
</template>