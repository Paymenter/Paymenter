<!-- Open up the VNC console using websocket -->

<iframe id="output-frame-id" style="width: 100%; height:400px; border: none;"></iframe>
  <script>
    async function getSrc() {
        // Disable cors
      const res = await fetch('{{ $websocket }}', {
        mode: 'no-cors',
        method: 'GET',
        headers: {
          'Authorization': 'PVEAPIToken={{ $vnc['ticket'] }}'
        }
      });
      const blob = await res.blob();
      const urlObject = URL.createObjectURL(blob);
      document.getElementById('output-frame-id').src = urlObject;
    }
    getSrc();
</script>
