export const rupiah = value => new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  minimumFractionDigits: 0,
}).format(value);

export const dateindo = (value, withTime = false) => {
  const libMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  
  const date = new Date(value);
  const getDay = date.getDate();
  const getMonth = date.getMonth();
  const getYear = date.getFullYear();

  const month = libMonth[getMonth];
  const year = (getYear < 1000) ? getYear + 1900 : getYear;
  let result = `${getDay} ${month} ${year} `;

  if (withTime) result += `${date.toLocaleTimeString('en-GB')}`
  
  return result;
}

export const periodindo = (period) => {
  if (!period) return
  const libMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

  const month = period.substring(4, 6);
  const year = period.substring(0, 4);

  return `${libMonth[parseInt(month) - 1]} ${year}`;
}

export default {
  rupiah,
  dateindo,
  periodindo,
}