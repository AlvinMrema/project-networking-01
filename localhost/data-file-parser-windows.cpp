/*
 * (C) STEM Loyola 2021. Part of Project #04
 *
 * This program:
 *    (1) parses data from a file (DATA_FILE)
 *    (2) opens a port at SERVER_PORT
 *    (3) forwards the data to any client request that will connect to the port
 *
 */

// TODO: Link "wsock32" during compilation. In CodeBlocks:
//       - Go to Settings > Compiler ...
//       - In "Global compiler settings" section, select "Linker settings" tab
//       - Click "Add" button and enter "wsock32" without quotes and click OK

#include <fstream>     // ifstream
#include <iostream>
#include <sstream>     // stringstream
#include <winsock2.h>  // WSA*, SOCK*, bind, listen, accept, recv, send, htons,
                       // htonl, closesocket

#define BUFFER_SIZE 8192        // Max bytes for a request
#define DATA_FILE   "data.csv"  // Local file containing data to upload
#define SERVER_PORT 9090        // Server will use this port for communication

using namespace std;

SOCKET serverSock;
WSADATA wsaData;   // Contains Windows Sockets API (WSA) data


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
 * Listen on a port for incoming connections
 * Resources:
 *    - Winsock main reference: https://docs.microsoft.com/en-us/windows/win32/api/winsock
 *    - Windows Sockets Error Codes: https://docs.microsoft.com/en-us/windows/win32/winsock/windows-sockets-error-codes-2
 *
 * PARAMS: - port: port number to listen on
 * @return: 0 if no error occurred, otherwise the value of socket error
 */
int listenOnPort (int port) {
    // Starting WSA and populate relevant info
    int error = WSAStartup (0x0202, &wsaData);

    if (error) {
        cerr << "[ERROR] Cannot start Winsock: " << WSAGetLastError() << endl;
        exit(error);
    }

    // Check if version 2 of Winsock is used
    if (wsaData.wVersion != 0x0202) {
        WSACleanup();
        cerr << "[ERROR] Wrong Winsock version: " << WSAGetLastError() << endl;
        exit(1);
    }

    // Configure a TCP address the server's socket will use
    SOCKADDR_IN addr;

    addr.sin_family = AF_INET;    // Address family for IP version 4
    addr.sin_port = htons(port);  // Designated port
    addr.sin_addr.s_addr = htonl(INADDR_ANY); // Accept connections from any IP

    // Create a socket and bind it to the defined address
    serverSock = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);

    if (serverSock == INVALID_SOCKET) {
        cerr << "[ERROR] Cannot create socket: " << WSAGetLastError() << endl;
        exit(1);
    }

    if (bind(serverSock, (LPSOCKADDR) &addr, sizeof(addr)) == SOCKET_ERROR) {
        // Usually happens when the port is already in use
        cerr << "[ERROR] Cannot bind: " << WSAGetLastError() << endl;
        exit(1);
    }

    // Start listening for incoming connections.
    // Will not return until a connection request is made
    return listen(serverSock, SOMAXCONN);
}


/*
 * Closes the socket and any connection made
 * PARAMS: - s: socket to close
 */
void cleanUp (SOCKET &s) {
    //Close the socket if it exists and clean up Winsock
    if (s) closesocket(s);

    WSACleanup();
}


/*
 * Main
 */
int main () {
    // Load all data from the file
    string data = loadData(DATA_FILE);

    // Wait for connections indefinitely and send data to each
    while (true) {
        listenOnPort(SERVER_PORT);

        //Accept a connection request from a client
        struct sockaddr clientAddr;
        int addrLength = sizeof(clientAddr);

        SOCKET clientSock = accept(serverSock, &clientAddr, &addrLength);

        if (clientSock < 0) {
            cerr << "[ERROR] Connecting to client: " << WSAGetLastError() << endl;
            exit(1);
        }

        // Extract request
        char request[BUFFER_SIZE];

        if (recv(clientSock, request, BUFFER_SIZE, 0)) {
            cout << "---> Request:\n" << request << endl;
        }

        // Send data to the client and exit
        if (send(clientSock, data.c_str(), data.size(), 0) == SOCKET_ERROR) {
            cerr << "[ERROR] Replying to client: " << WSAGetLastError() << endl;
        }else{
            cout << "---> Data sent" << endl;
        }

        cleanUp(clientSock);
    }

    cleanUp(serverSock);

    return 0;
}
