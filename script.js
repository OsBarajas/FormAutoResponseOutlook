const zoomRooms = [
    { name: "ACC20", url: "https://mydomain.zoom.us/my/ACC20", available: true, conferenceId: "123456", phoneNumbers: "123-456-7890" },
    { name: "ACC21", url: "https://mydomain.zoom.us/my/ACC21", available: false, conferenceId: "223456", phoneNumbers: "123-456-7891" },
    { name: "ACC22", url: "https://mydomain.zoom.us/my/ACC22", available: false, conferenceId: "323456", phoneNumbers: "123-456-7892" },
    { name: "ACC23", url: "https://mydomain.zoom.us/my/ACC23", available: false, conferenceId: "423456", phoneNumbers: "123-456-7893" },
    { name: "ACC24", url: "https://mydomain.zoom.us/my/ACC24", available: false, conferenceId: "523456", phoneNumbers: "123-456-7894" },
    { name: "ACC25", url: "https://mydomain.zoom.us/my/ACC25", available: false, conferenceId: "623456", phoneNumbers: "123-456-7895" },
    { name: "ACC26", url: "https://mydomain.zoom.us/my/ACC26", available: false, conferenceId: "723456", phoneNumbers: "123-456-7896" },
    { name: "ACC27", url: "https://mydomain.zoom.us/my/ACC27", available: false, conferenceId: "823456", phoneNumbers: "123-456-7897" },
    { name: "ACC28", url: "https://mydomain.zoom.us/my/ACC28", available: false, conferenceId: "923456", phoneNumbers: "123-456-7898" },
    { name: "ACC29", url: "https://mydomain.zoom.us/my/ACC29", available: false, conferenceId: "1023456", phoneNumbers: "123-456-7899" }
];

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#zoomRoomsTable tbody');

    zoomRooms.forEach(room => {
        const row = document.createElement('tr');
        const statusClass = room.available ? 'available' : 'not-available';
        const statusText = room.available ? 'Available' : 'Not Available';
        row.innerHTML = `
            <td>${room.name}</td>
            <td><a href="${room.url}" target="_blank">${room.url}</a></td>
            <td class="${statusClass}">${statusText}</td>
            <td><button ${!room.available ? 'disabled' : ''} onclick="createMeetingRequest('${room.name}', '${room.url}', '${room.conferenceId}', '${room.phoneNumbers}')">Continue</button></td>
        `;
        tableBody.appendChild(row);
    });
});

function createMeetingRequest(roomName, roomUrl, conferenceId, phoneNumbers) {
    const subject = "Coordination Call";
    const htmlDescription = `
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h1 style="text-align: center; color: #000;">Coordination Call</h1>
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
      <thead>
        <tr style="background-color: #1e00fe; color: #fff; text-align: left;">
          <th style="padding: 10px; border: 1px solid #ddd;">Category</th>
          <th style="padding: 10px; border: 1px solid #ddd;">Details</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding: 10px; border: 1px solid #ddd;">Title</td>
          <td style="padding: 10px; border: 1px solid #ddd;"></td>
        </tr>
        <tr style="background-color: #f9f9f9;">
          <td style="padding: 10px; border: 1px solid #ddd;">Team</td>
          <td style="padding: 10px; border: 1px solid #ddd;"></td>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid #ddd;">Impact</td>
          <td style="padding: 10px; border: 1px solid #ddd;"></td>
        </tr>
        <tr style="background-color: #f9f9f9;">
          <td style="padding: 10px; border: 1px solid #ddd;">Member numbers affected</td>
          <td style="padding: 10px; border: 1px solid #ddd;"></td>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid #ddd;">Services / applications affected</td>
          <td style="padding: 10px; border: 1px solid #ddd;"></td>
        </tr>
      </tbody>
    </table>
    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">
    <h2 style="color: #000;">Join Us</h2>
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
      <tbody>
        <tr>
          <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Zoom Room</strong></td>
          <td style="padding: 10px; border: 1px solid #ddd;"><a href="${roomUrl}" style="color: #2980B9; text-decoration: none;" target="_blank">${roomUrl}</a></td>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Conference ID</strong></td>
          <td style="padding: 10px; border: 1px solid #ddd;">${conferenceId}</td>
        </tr>
        <tr>
          <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Phone Numbers</strong></td>
          <td style="padding: 10px; border: 1px solid #ddd;">${phoneNumbers}</td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
    `;

    const plainDescription = `
Coordination Call

INCs:
- Title:
- Team:
- Impact:
- Member numbers affected:
- Services / applications affected:

---------------------------------------------------------
                       JOIN US:
---------------------------------------------------------

Zoom Room: ${roomUrl}
Conference ID: ${conferenceId}
Phone Numbers: ${phoneNumbers}
    `;

    const startDate = new Date();
    const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // 1-hour duration

    const icsContent = `
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Your Company//NONSGML v1.0//EN
BEGIN:VEVENT
UID:${Date.now()}@yourdomain.com
DTSTAMP:${formatDate(startDate)}
DTSTART:${formatDate(startDate)}
DTEND:${formatDate(endDate)}
SUMMARY:${subject}
DESCRIPTION:${plainDescription.replace(/\n/g, '\\n')}
X-ALT-DESC;FMTTYPE=text/html:${htmlDescription.replace(/\n/g, '').replace(/"/g, '\\"')}
LOCATION:${roomName}
ATTENDEE;CN="CCOPS";RSVP=TRUE:mailto:CCOPS@mydomain.com
END:VEVENT
END:VCALENDAR
`;

    const blob = new Blob([icsContent], { type: 'text/calendar' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'meetreq.ics';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function formatDate(date) {
    return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
}