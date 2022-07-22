export const rupiah = value => new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  minimumFractionDigits: 0,
}).format(value);

export const dateindo = value => {
  const libMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  
  const date = new Date(value);
  const getDay = date.getDate();
  const getMonth = date.getMonth();
  const getYear = date.getFullYear();

  const month = libMonth[getMonth];
  const year = (getYear < 1000) ? getYear + 1900 : getYear;
  return `${getDay} ${month} ${year}`;
}

export default {
  rupiah,
  dateindo,
}