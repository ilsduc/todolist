export function timestampToDate(timestamp) {
  if (!timestamp) { return; }
  // split into array
  let timestampParts= timestamp.split(/[- :]/);
  // decrement
  timestampParts[1]--;

  const dataObject = new Date(...timestampParts);
  return dataObject.getDate().toString() + "/" + dataObject.getMonth().toString() + "/" + dataObject.getFullYear().toString();
}

export function timestampToDay(timestamp) {
  if (!timestamp) { return; }
  let timestampParts= timestamp.split(/[- :]/);
  timestampParts[1]--;
  const dataObject = new Date(...timestampParts);
  return dataObject.getDate();
}

export function timestampToMonth(timestamp) {
  if (!timestamp) { return; }
  let timestampParts= timestamp.split(/[- :]/);
  timestampParts[1]--;

  const dataObject = new Date(...timestampParts);
  const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AOU', 'OCT', 'NOV', 'DEC'];
  return months[dataObject.getMonth()];
}

export function boolToInt(object) {
  for (var key in object) {
    if (typeof object[key] === 'boolean') {
      object[key] = object[key] | 0;
    }
    if (typeof object[key] === 'object') {
      object[key] = object[key] === null ? 0 : object[key];
    }
  }
  return object;
}
