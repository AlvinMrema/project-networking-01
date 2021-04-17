/*
 * (C) STEM Loyola 2021. Part of Project #04
 *
 * This program:
 *    (1) parses data from a file (DATA_FILE)
 *    (2) opens a port at SERVER_PORT
 *    (3) forwards the data to any client request that will connect to the port
 *
 */

#include <fstream>       // ifstream
#include <iostream>
#include <sstream>       // stringstream
#include <netinet/in.h>  // htons, htonl, sockaddr_in, IPPROTO_TCP, INADDR_ANY
#include <sys/socket.h>  // socket, bind, listen, accept, recv, send, socklen_t, AF_INET, SOMAXCONN
#include <unistd.h>      // close

#define BUFFER_SIZE 8192        // Max bytes for a request
#define DATA_FILE   "data.csv"  // Local file containing data to upload
#define SERVER_PORT 9090        // Server will use this port for communication

using namespace std;


/* NOTE: If you get errors (e.g. ERR_INVALID_HTTP_RESPONSE) when accessing
 *       "localhost:9090" in a browser, use one of the following commands for
 *       in a Terminal. Port should be the same as one in SERVER_PORT
 *
 *       wget -O - localhost:9090
 *       telnet localhost 9090
 */

/*
 * Loads all data to send on each request
 *
 * PARAMS: - filename: data file name
 * @return: data file contents
 */
string loadData (string filename) {
    string data;

    try {
        ifstream file(filename);
        stringstream buffer;
        buffer << file.rdbuf();

        data = buffer.str();
    } catch (...) {
        cerr << "[ERROR] Reading file: " << filename << endl;
        data = "";
    }

    return data;
}


/*
 * Initialize socket and bind to an address and port
 *
 * PARAMS: - port: port number to listen on
 */
int initializeSocket (int port) {
    // Create a TCP socket
    int serverSock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);

    if (serverSock < 0) {
        cerr << "[ERROR] Cannot create socket" << endl;
        exit(1);
    }

    // Bind the socket to a TCP/IP address (IP & port)
    struct sockaddr_in addr;

    addr.sin_family = AF_INET;    // Address family for IP version 4
    addr.sin_port = htons(port);  // Designated port
    addr.sin_addr.s_addr = htonl(INADDR_ANY); // Accept connections from any IP

    if (bind(serverSock, (struct sockaddr*) &addr, sizeof(addr)) < 0) {
        // Usually happens when the port is already in use. Try the following
        // command to see if port 9090 is already in use
        //
        // netstat -anp | grep 9090
        cerr << "[ERROR] Cannot bind " << endl;
        exit(1);
    }

    return serverSock;
}


/*
 * Main
 */
int main () {
    // Load all data from the file
    string data = loadData(DATA_FILE);

    int serverSock = initializeSocket(SERVER_PORT);

    // Wait for connections indefinitely and send data to each
    while (true) {
        // Listen for incoming connections.
        listen(serverSock, SOMAXCONN);  // Will not return until a connection request is made

        //Accept a connection request from a client
        struct sockaddr clientAddr;
        socklen_t addrLength = sizeof(clientAddr);

        int clientSock = accept(serverSock, &clientAddr, &addrLength);

        if (clientSock < 0) {
            cerr << "[ERROR] Connecting to client" << endl;
            exit(1);
        }

        // Extract request
        char request[BUFFER_SIZE];

        if (recv(clientSock, request, BUFFER_SIZE, 0) < 0) {
            cerr << "[ERROR] Receiving a client request" << endl;
        } else {
            cout << "---> Request:\n" << request << endl;
        }

        // Send data to the client and exit
        if (send(clientSock, data.c_str(), data.size(), 0) < 0) {
            cerr << "[ERROR] Replying to client" << endl;
        } else {
            cout << "---> Data sent" << endl;
        }

        close(clientSock);  // Terminate client connection
    }

    close(serverSock);

    return 0;
}
