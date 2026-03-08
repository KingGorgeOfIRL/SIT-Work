from threading import Thread
from http.server import ThreadingHTTPServer, SimpleHTTPRequestHandler

from dnslib import RR, QTYPE, A
from dnslib.server import BaseResolver, DNSServer


WEB_IP = "10.68.176.110"
DOMAIN = "stonks.sitct.net."


class SimpleResolver(BaseResolver):
    def resolve(self, request, handler):
        reply = request.reply()
        qname = str(request.q.qname)
        qtype = QTYPE[request.q.qtype]

        if qname == DOMAIN and qtype in ("A", "ANY"):
            reply.add_answer(
                RR(
                    DOMAIN,
                    QTYPE.A,
                    rdata=A(WEB_IP),
                    ttl=60,
                )
            )

        return reply


def start_dns():
    resolver = SimpleResolver()
    server = DNSServer(resolver, port=53, address="0.0.0.0")
    server.start()


def start_http():
    httpd = ThreadingHTTPServer(("0.0.0.0", 80), SimpleHTTPRequestHandler)
    print("HTTP server running on port 80")
    httpd.serve_forever()


if __name__ == "__main__":
    dns_thread = Thread(target=start_dns, daemon=True)
    dns_thread.start()

    print("DNS server running on port 53")
    start_http()
